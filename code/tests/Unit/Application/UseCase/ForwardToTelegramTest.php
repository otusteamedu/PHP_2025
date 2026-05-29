<?php

declare(strict_types=1);

namespace MkdBot\Tests\Unit\Application\UseCase;

use MkdBot\Application\DTO\ForwardMessageDTO;
use MkdBot\Application\UseCase\ForwardToTelegram;
use MkdBot\Domain\Interface\TelegramBotClientInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ForwardToTelegramTest extends TestCase
{
    private const MAX_CHANNEL_NAME = 'МКД XXX';

    /**
     * Создаёт ForwardToTelegram с тестовыми параметрами (включая имя канала Max)
     */
    private function createUseCase(
        TelegramBotClientInterface $telegramBot,
        LoggerInterface $logger,
    ): ForwardToTelegram {
        return new ForwardToTelegram($telegramBot, $logger, '@test_channel', self::MAX_CHANNEL_NAME);
    }

    /**
     * Ожидаемый заголовок-префикс для пересылаемых сообщений
     */
    private function expectedHeader(): string
    {
        return '📢 МКД-Бот: дублирование из канала Max «' . self::MAX_CHANNEL_NAME . '»';
    }

    public function testForwardsTextMessage(): void
    {
        $telegramBot = $this->createMock(TelegramBotClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);

        $telegramBot->expects($this->once())
            ->method('sendMessage')
            ->with($this->callback(function (array $params) {
                return $params['chat_id'] === '@test_channel'
                && str_contains($params['text'], $this->expectedHeader())
                && str_contains($params['text'], 'Hello world');
            }));

        $useCase = $this->createUseCase($telegramBot, $logger);

        $dto = new ForwardMessageDTO(
            text: 'Hello world',
            attachments: [],
            sourceMessageMid: 'mid.1',
            chatId: 123,
            chatType: 'channel',
            messageUrl: null,
        );

        $useCase->execute($dto);
    }

    public function testAppendsOriginalLink(): void
    {
        $telegramBot = $this->createMock(TelegramBotClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);

        $telegramBot->expects($this->once())
            ->method('sendMessage')
            ->with($this->callback(function (array $params) {
                return str_contains($params['text'], '🔗 Оригинал: https://max.ru/post/1');
            }));

        $useCase = $this->createUseCase($telegramBot, $logger);

        $dto = new ForwardMessageDTO(
            text: 'Post text',
            attachments: [],
            sourceMessageMid: 'mid.1',
            chatId: 123,
            chatType: 'channel',
            messageUrl: 'https://max.ru/post/1',
        );

        $useCase->execute($dto);
    }

    public function testForwardsPhotoWithCaption(): void
    {
        $telegramBot = $this->createMock(TelegramBotClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);

        $telegramBot->expects($this->once())
            ->method('sendPhoto')
            ->with($this->callback(function (array $params) {
                return $params['photo'] === 'https://max.ru/photo1.jpg'
                && str_contains($params['caption'], 'Photo post');
            }));

        $useCase = $this->createUseCase($telegramBot, $logger);

        $dto = new ForwardMessageDTO(
            text: 'Photo post',
            attachments: [
                ['type' => 'photo', 'url' => 'https://max.ru/photo1.jpg', 'token' => null, 'filename' => null, 'size' => null],
            ],
            sourceMessageMid: 'mid.2',
            chatId: 123,
            chatType: 'channel',
            messageUrl: null,
        );

        $useCase->execute($dto);
    }

    public function testForwardsDocumentWithCaption(): void
    {
        $telegramBot = $this->createMock(TelegramBotClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);

        $telegramBot->expects($this->once())
            ->method('sendDocument')
            ->with($this->callback(function (array $params) {
                return $params['document'] === 'https://max.ru/doc1.pdf'
                && str_contains($params['caption'], 'Doc post');
            }));

        $useCase = $this->createUseCase($telegramBot, $logger);

        $dto = new ForwardMessageDTO(
            text: 'Doc post',
            attachments: [
                ['type' => 'file', 'url' => 'https://max.ru/doc1.pdf', 'token' => null, 'filename' => 'doc1.pdf', 'size' => 1024],
            ],
            sourceMessageMid: 'mid.3',
            chatId: 123,
            chatType: 'channel',
            messageUrl: null,
        );

        $useCase->execute($dto);
    }

    public function testSkipsPhotoWithEmptyUrlAndFallsBackToText(): void
    {
        $telegramBot = $this->createMock(TelegramBotClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);

        // При пустом URL фото пропускаются, документов нет — ни sendMessage, ни sendPhoto не вызываются
        $telegramBot->expects($this->never())->method('sendPhoto');
        $telegramBot->expects($this->never())->method('sendMessage');

        $useCase = $this->createUseCase($telegramBot, $logger);

        $dto = new ForwardMessageDTO(
            text: 'Photo with no url',
            attachments: [
                ['type' => 'photo', 'url' => '', 'token' => null, 'filename' => null, 'size' => null],
            ],
            sourceMessageMid: 'mid.4',
            chatId: 123,
            chatType: 'channel',
            messageUrl: null,
        );

        $useCase->execute($dto);
    }

    // ========== Крайние случаи ==========

    /**
     * Несколько фото — первое с caption (текст + ссылка на оригинал), остальные без caption
     */
    public function testMultiplePhotosFirstWithCaptionRestWithout(): void
    {
        $telegramBot = $this->createMock(TelegramBotClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);

        // Ожидаем 2 вызова sendPhoto: первый с caption, второй — без
        $callCount = 0;
        $telegramBot->expects($this->exactly(2))
            ->method('sendPhoto')
            ->with($this->callback(function (array $params) use (&$callCount) {
                $callCount++;
                if ($callCount === 1) {
                    // Первое фото — с caption
                    return $params['photo'] === 'https://max.ru/photo1.jpg'
                    && isset($params['caption'])
                    && str_contains($params['caption'], 'Post with photos')
                    && str_contains($params['caption'], '🔗 Оригинал: https://max.ru/post/99');
                }
                // Второе фото — без caption
                return $params['photo'] === 'https://max.ru/photo2.jpg'
                && !isset($params['caption']);
            }));

        $useCase = $this->createUseCase($telegramBot, $logger);

        $dto = new ForwardMessageDTO(
            text: 'Post with photos',
            attachments: [
                ['type' => 'photo', 'url' => 'https://max.ru/photo1.jpg', 'token' => null, 'filename' => null, 'size' => null],
                ['type' => 'photo', 'url' => 'https://max.ru/photo2.jpg', 'token' => null, 'filename' => null, 'size' => null],
            ],
            sourceMessageMid: 'mid.multi',
            chatId: 123,
            chatType: 'channel',
            messageUrl: 'https://max.ru/post/99',
        );

        $useCase->execute($dto);
    }

    /**
     * Документ без URL — fallback текстовое уведомление «📎 Документ: {filename} ({size} байт)»
     */
    public function testDocumentWithoutUrlSendsFallbackTextNotification(): void
    {
        $telegramBot = $this->createMock(TelegramBotClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);

        // Ожидаем fallback-уведомление через sendMessage
        $telegramBot->expects($this->once())
            ->method('sendMessage')
            ->with($this->callback(function (array $params) {
                return $params['chat_id'] === '@test_channel'
                && str_contains($params['text'], '📎 Документ: report.pdf (2048 байт)');
            }));
        // sendDocument НЕ вызывается
        $telegramBot->expects($this->never())->method('sendDocument');

        $logger->expects($this->once())->method('warning')
            ->with($this->stringContains('Документ без URL'));

        $useCase = $this->createUseCase($telegramBot, $logger);

        $dto = new ForwardMessageDTO(
            text: 'Doc without url',
            attachments: [
                ['type' => 'file', 'url' => '', 'token' => null, 'filename' => 'report.pdf', 'size' => 2048],
            ],
            sourceMessageMid: 'mid.nourl',
            chatId: 123,
            chatType: 'channel',
            messageUrl: null,
        );

        $useCase->execute($dto);
    }

    /**
     * Обрезка текста — при превышении 4096 символов для Telegram -> «...текст сокращён»
     */
    public function testTextTruncationWhenExceedingTelegramLimit(): void
    {
        $telegramBot = $this->createMock(TelegramBotClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);

        // Текст длиной 4100 символов — должен быть обрезан с пометкой «...текст сокращён»
        // Замечание: truncateText использует константу 17 для суффикса,
        // но реальная длина "\n...текст сокращён" = 18 символов — итоговая длина может быть на 1 больше лимита
        $longText = str_repeat('А', 4100);

        $telegramBot->expects($this->once())
            ->method('sendMessage')
            ->with($this->callback(function (array $params) {
                $text = $params['text'];
                // Текст обрезан и содержит пометку о сокращении
                return mb_strlen($text) < 4100
                && str_contains($text, '...текст сокращён');
            }));

        $useCase = $this->createUseCase($telegramBot, $logger);

        $dto = new ForwardMessageDTO(
            text: $longText,
            attachments: [],
            sourceMessageMid: 'mid.long',
            chatId: 123,
            chatType: 'channel',
            messageUrl: null,
        );

        $useCase->execute($dto);
    }

    /**
     * Обрезка caption — при превышении 1024 символов для sendPhoto -> «...текст сокращён»
     */
    public function testCaptionTruncationWhenExceedingPhotoCaptionLimit(): void
    {
        $telegramBot = $this->createMock(TelegramBotClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);

        // Текст длиной 1100 символов — caption для фото должен быть обрезан с пометкой
        // Замечание: константа суффикса = 17, но реальная длина "\n...текст сокращён" = 18
        $longCaption = str_repeat('Б', 1100);

        $telegramBot->expects($this->once())
            ->method('sendPhoto')
            ->with($this->callback(function (array $params) {
                $caption = $params['caption'] ?? '';
                // Caption обрезан и содержит пометку о сокращении
                return mb_strlen($caption) < 1100
                && str_contains($caption, '...текст сокращён');
            }));

        $useCase = $this->createUseCase($telegramBot, $logger);

        $dto = new ForwardMessageDTO(
            text: $longCaption,
            attachments: [
                ['type' => 'photo', 'url' => 'https://max.ru/photo.jpg', 'token' => null, 'filename' => null, 'size' => null],
            ],
            sourceMessageMid: 'mid.longcap',
            chatId: 123,
            chatType: 'channel',
            messageUrl: null,
        );

        $useCase->execute($dto);
    }

    /**
     * Ссылка на оригинал — при наличии messageUrl добавляется «🔗 Оригинал: {url}»
     */
    public function testOriginalLinkAppendedWhenMessageUrlPresent(): void
    {
        $telegramBot = $this->createMock(TelegramBotClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);

        $telegramBot->expects($this->once())
            ->method('sendMessage')
            ->with($this->callback(function (array $params) {
                return str_contains($params['text'], '🔗 Оригинал: https://max.ru/post/42')
                && str_contains($params['text'], 'Текст сообщения');
            }));

        $useCase = $this->createUseCase($telegramBot, $logger);

        $dto = new ForwardMessageDTO(
            text: 'Текст сообщения',
            attachments: [],
            sourceMessageMid: 'mid.orig',
            chatId: 123,
            chatType: 'channel',
            messageUrl: 'https://max.ru/post/42',
        );

        $useCase->execute($dto);
    }

    /**
     * Пустое сообщение (нет текста и вложений) — отправляется заголовок-префикс
     */
    public function testEmptyMessageWithNoTextAndNoAttachments(): void
    {
        $telegramBot = $this->createMock(TelegramBotClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);

        // Пустой текст без вложений — отправляется заголовок-префикс
        $telegramBot->expects($this->once())
            ->method('sendMessage')
            ->with($this->callback(function (array $params) {
                return $params['chat_id'] === '@test_channel'
                && $params['text'] === $this->expectedHeader();
            }));

        $useCase = $this->createUseCase($telegramBot, $logger);

        $dto = new ForwardMessageDTO(
            text: '',
            attachments: [],
            sourceMessageMid: 'mid.empty',
            chatId: 123,
            chatType: 'channel',
            messageUrl: null,
        );

        $useCase->execute($dto);
    }

    /**
     * Смешанные вложения — фото + документ в одном сообщении
     * Первое фото с caption, документ без caption (текст уже отправлен с фото)
     */
    public function testMixedAttachmentsPhotoAndDocument(): void
    {
        $telegramBot = $this->createMock(TelegramBotClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);

        // Ожидаем: 1 sendPhoto (с caption) + 1 sendDocument (без caption)
        $telegramBot->expects($this->once())
            ->method('sendPhoto')
            ->with($this->callback(function (array $params) {
                return $params['photo'] === 'https://max.ru/photo.jpg'
                && isset($params['caption'])
                && str_contains($params['caption'], 'Mixed message');
            }));

        $telegramBot->expects($this->once())
            ->method('sendDocument')
            ->with($this->callback(function (array $params) {
                return $params['document'] === 'https://max.ru/doc.pdf'
                && !isset($params['caption']);
            }));

        $useCase = $this->createUseCase($telegramBot, $logger);

        $dto = new ForwardMessageDTO(
            text: 'Mixed message',
            attachments: [
                ['type' => 'photo', 'url' => 'https://max.ru/photo.jpg', 'token' => null, 'filename' => null, 'size' => null],
                ['type' => 'file', 'url' => 'https://max.ru/doc.pdf', 'token' => null, 'filename' => 'doc.pdf', 'size' => 512],
            ],
            sourceMessageMid: 'mid.mixed',
            chatId: 123,
            chatType: 'channel',
            messageUrl: null,
        );

        $useCase->execute($dto);
    }

    /**
     * Документ с размером > 50 МБ — fallback текстовое уведомление о превышении лимита
     */
    public function testDocumentExceeding50MBSizeLimitSendsFallbackNotification(): void
    {
        $telegramBot = $this->createMock(TelegramBotClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);

        $telegramBot->expects($this->once())
            ->method('sendMessage')
            ->with($this->callback(function (array $params) {
                return str_contains($params['text'], '📎 Документ: big.pdf')
                && str_contains($params['text'], 'превышает лимит 50 МБ');
            }));
        $telegramBot->expects($this->never())->method('sendDocument');

        $logger->expects($this->once())->method('warning')
            ->with($this->stringContains('превышает 50 МБ'));

        $useCase = $this->createUseCase($telegramBot, $logger);

        $dto = new ForwardMessageDTO(
            text: 'Big file post',
            attachments: [
                ['type' => 'file', 'url' => 'https://max.ru/big.pdf', 'token' => null, 'filename' => 'big.pdf', 'size' => 52428801],
            ],
            sourceMessageMid: 'mid.big',
            chatId: 123,
            chatType: 'channel',
            messageUrl: null,
        );

        $useCase->execute($dto);
    }

    /**
     * Тип вложения 'document' (из маппера) — обрабатывается как 'file'
     * Маппер ставит type='document', а ForwardToTelegram должен принимать и 'document', и 'file'
     */
    public function testDocumentTypeFromMapperTreatedAsFile(): void
    {
        $telegramBot = $this->createMock(TelegramBotClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);

        // Ожидаем sendDocument с caption — тип 'document' должен обрабатываться
        $telegramBot->expects($this->once())
            ->method('sendDocument')
            ->with($this->callback(function (array $params) {
                return $params['document'] === 'https://max.ru/doc.pdf'
                && str_contains($params['caption'], 'Doc from mapper');
            }));

        $useCase = $this->createUseCase($telegramBot, $logger);

        $dto = new ForwardMessageDTO(
            text: 'Doc from mapper',
            attachments: [
                ['type' => 'document', 'url' => 'https://max.ru/doc.pdf', 'token' => null, 'filename' => 'doc.pdf', 'size' => 1024],
            ],
            sourceMessageMid: 'mid.docmap',
            chatId: 123,
            chatType: 'channel',
            messageUrl: null,
        );

        $useCase->execute($dto);
    }

    /**
     * Неизвестный тип вложения — логируется предупреждение,
     * сообщение не отправляется (attachments не empty, но нет фото/документов)
     */
    public function testUnknownAttachmentTypeLogsWarning(): void
    {
        $telegramBot = $this->createMock(TelegramBotClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);

        // Неизвестный тип вложения — логируется предупреждение
        $logger->expects($this->once())->method('warning')
            ->with($this->stringContains('Неизвестный тип вложения: video'));

        // Ни sendMessage, ни sendPhoto, ни sendDocument не вызываются —
        // attachments не пустой, но фото и документы не найдены
        $telegramBot->expects($this->never())->method('sendMessage');
        $telegramBot->expects($this->never())->method('sendPhoto');
        $telegramBot->expects($this->never())->method('sendDocument');

        $useCase = $this->createUseCase($telegramBot, $logger);

        $dto = new ForwardMessageDTO(
            text: 'Video post',
            attachments: [
                ['type' => 'video', 'url' => 'https://max.ru/video.mp4', 'token' => null, 'filename' => null, 'size' => null],
            ],
            sourceMessageMid: 'mid.video',
            chatId: 123,
            chatType: 'channel',
            messageUrl: null,
        );

        $useCase->execute($dto);
    }
}
