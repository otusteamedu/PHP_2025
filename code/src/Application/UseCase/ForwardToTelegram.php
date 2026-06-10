<?php

declare(strict_types=1);

namespace MkdBot\Application\UseCase;

use MkdBot\Application\DTO\ForwardMessageDTO;
use MkdBot\Domain\Interface\TelegramBotClientInterface;
use Psr\Log\LoggerInterface;

/**
 * Оркестрация дублирования Max->Telegram
 * Вызывается из RabbitMQ consumer-а
 */
class ForwardToTelegram
{
    private const MAX_TELEGRAM_TEXT_LENGTH = 4096;
    private const MAX_CAPTION_LENGTH = 1024;
    private const MAX_FILE_SIZE = 52428800; // 50 МБ
    private const ATTACHMENT_SEND_DELAY_MS = 500;

    public function __construct(
        private readonly TelegramBotClientInterface $telegramBot,
        private readonly LoggerInterface $logger,
        private readonly string $telegramChannel,
        private readonly string $maxChannelName = '',
    ) {
    }

    /**
     * Формирует заголовок-префикс для пересылаемого сообщения
     */
    private function formatForwardHeader(): string
    {
        if ($this->maxChannelName !== '') {
            return "📢 МКД-Бот: дублирование из канала Max «{$this->maxChannelName}»";
        }
        return '📢 МКД-Бот: дублирование из канала Max';
    }

    /**
     * Дублирует сообщение из Max в Telegram-канал
     */
    public function execute(ForwardMessageDTO $dto): void
    {
        $header = $this->formatForwardHeader();
        $text = $dto->text;

        if ($text !== '') {
            $text = $header . "\n\n" . $text;
        } else {
            $text = $header;
        }

        if ($dto->messageUrl !== null && $dto->messageUrl !== '') {
            $text .= "\n\n🔗 Оригинал: {$dto->messageUrl}";
        }

        if (empty($dto->attachments)) {
            $this->sendTextMessage($text);
            return;
        }

        $photos = [];
        $documents = [];

        foreach ($dto->attachments as $attachment) {
            $type = $attachment['type'];
            // 'document' — из маппера, 'file' — из Max API
            match ($type) {
                'photo' => $photos[] = $attachment,
                'file', 'document' => $documents[] = $attachment,
                default => $this->logger->warning("Неизвестный тип вложения: {$type}"),
            };
        }

        // Отправляем фото (каждое отдельным сообщением)
        if (!empty($photos)) {
            $this->sendPhotos($photos, $text);
            // Текст уже отправлен с первым фото — документы без caption
            if (!empty($documents)) {
                $this->sendDocuments($documents, '');
            }
        } elseif (!empty($documents)) {
            $this->sendDocuments($documents, $text);
        }
    }

    /**
     * Отправляет текстовое сообщение в Telegram
     */
    private function sendTextMessage(string $text): void
    {
        $text = $this->truncateText($text, self::MAX_TELEGRAM_TEXT_LENGTH);
        $this->telegramBot->sendMessage([
            'chat_id' => $this->telegramChannel,
            'text' => $text,
            'parse_mode' => 'HTML',
        ]);
    }

    /**
     * Отправляет фото в Telegram (каждое отдельным сообщением)
     * Первое фото — с caption (текст + ссылка на оригинал), остальные — без
     */
    private function sendPhotos(array $photos, string $caption): void
    {
        foreach ($photos as $index => $photo) {
            $url = $photo['url'] ?? null;
            if ($url === null || $url === '') {
                $this->logger->warning("Фото без URL, пропуск");
                continue;
            }

            $params = [
                'chat_id' => $this->telegramChannel,
                'photo' => $url,
            ];

            if ($index === 0 && $caption !== '') {
                $params['caption'] = $this->truncateText($caption, self::MAX_CAPTION_LENGTH);
                $params['parse_mode'] = 'HTML';
            }

            $this->telegramBot->sendPhoto($params);

            $this->delayBetweenAttachments($index, count($photos));
        }
    }

    /**
     * Отправляет документы в Telegram
     * Документы всегда скачиваются и отправляются через InputFile
     * Если URL отсутствует — fallback: текстовое уведомление
     */
    private function sendDocuments(array $documents, string $caption): void
    {
        foreach ($documents as $index => $document) {
            $url = $document['url'] ?? null;
            $filename = $document['filename'] ?? 'document';
            $size = $document['size'] ?? 0;

            if ($url === null || $url === '') {
                $this->logger->warning("Документ без URL, fallback-уведомление: {$filename}");
                $fallbackText = "📎 Документ: {$filename} ({$size} байт)";
                $this->telegramBot->sendMessage([
                    'chat_id' => $this->telegramChannel,
                    'text' => $fallbackText,
                ]);
                continue;
            }

            // Проверка лимита размера файла (50 МБ)
            if ($size > self::MAX_FILE_SIZE) {
                $this->logger->warning("Документ превышает 50 МБ: {$filename} ({$size} байт)");
                $this->telegramBot->sendMessage([
                    'chat_id' => $this->telegramChannel,
                    'text' => "📎 Документ: {$filename} ({$size} байт) — превышает лимит 50 МБ",
                ]);
                continue;
            }

            // Скачивание и отправка через InputFile реализованы в TelegramBotClient::sendDocument()
            $params = [
                'chat_id' => $this->telegramChannel,
                'document' => $url,
                'filename' => $filename,
            ];

            if ($index === 0 && $caption !== '') {
                $params['caption'] = $this->truncateText($caption, self::MAX_CAPTION_LENGTH);
                $params['parse_mode'] = 'HTML';
            }

            $this->telegramBot->sendDocument($params);

            $this->delayBetweenAttachments($index, count($documents));
        }
    }

    /**
     * Выдерживает задержку между отправками вложений для снижения
     * вероятности Telegram rate-limit (HTTP 429) при пакетной отправке большого числа файлов.
     */
    private function delayBetweenAttachments(int $currentIndex, int $totalCount): void
    {
        if ($currentIndex < $totalCount - 1) {
            usleep(self::ATTACHMENT_SEND_DELAY_MS * 1000);
        }
    }

    /**
     * Обрезает текст до указанного лимита с пометкой «...текст сокращён»
     */
    private function truncateText(string $text, int $maxLength): string
    {
        if (mb_strlen($text) <= $maxLength) {
            return $text;
        }

        return mb_substr($text, 0, $maxLength - 17) . "\n...текст сокращён";
    }
}
