<?php

declare(strict_types=1);

namespace MkdBot\Infrastructure\MaxBot;

use MaxMessenger\Bot\Exceptions\Validation\MaxLengthException;
use MaxMessenger\Bot\MaxApiClient;
use MaxMessenger\Bot\Models\Enums\Intent;
use MaxMessenger\Bot\Models\Enums\SenderAction;
use MaxMessenger\Bot\Models\Requests\CallbackAnswer;
use MaxMessenger\Bot\Models\Requests\NewMessageBody;
use MaxMessenger\Bot\Models\Responses\SendMessageResult as LibSendMessageResult;
use MkdBot\Domain\Interface\MaxBotClientInterface;
use MkdBot\Domain\ValueObject\SendMessageResult as DomainSendMessageResult;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * Адаптер клиента Max Bot API — делегирует final-класс MaxApiClient через композицию
 * Принимает простые типы (string, int, array), маппит в библиотечные модели
 */
class MaxBotClient implements MaxBotClientInterface
{
    private const MAX_TEXT_LENGTH = 4000;

    private const TRUNCATE_SUFFIX = "\n...текст сокращён";

    private MaxApiClient $client;

    public function __construct(
        MaxApiClient $client,
        private readonly LoggerInterface $logger,
    ) {
        $this->client = $client;
    }

    public function sendMessageToUser(int $userId, string $text, bool $disableLinkPreview = false): DomainSendMessageResult
    {
        $text = $this->truncateText($text);

        try {
            $message = new NewMessageBody($text);
        } catch (MaxLengthException $e) {
            $this->logger->warning("MaxBot: MaxLengthException в sendMessageToUser — fallback обрезка текста");
            $text = $this->forceTruncateText($text);
            $message = new NewMessageBody($text);
        }

        $this->logger->debug("MaxBot: sendMessageToUser userId={$userId}, textLength=" . mb_strlen($text));

        $result = $this->client->sendMessageToUser($userId, $message, $disableLinkPreview);

        return $this->mapResult($result);
    }

    public function sendMessageToChat(int $chatId, string $text, bool $disableLinkPreview = false): DomainSendMessageResult
    {
        $text = $this->truncateText($text);

        try {
            $message = new NewMessageBody($text);
        } catch (MaxLengthException $e) {
            $this->logger->warning("MaxBot: MaxLengthException в sendMessageToChat — fallback обрезка текста");
            $text = $this->forceTruncateText($text);
            $message = new NewMessageBody($text);
        }

        $this->logger->debug("MaxBot: sendMessageToChat chatId={$chatId}, textLength=" . mb_strlen($text));

        $result = $this->client->sendMessageToChat($chatId, $message, $disableLinkPreview);

        return $this->mapResult($result);
    }

    /**
     * Отправка сообщения с inline-клавиатурой
     * $text — опционален (NewMessageBody позволяет отправить только клавиатуру)
     * $buttons — массив кнопок: [['text' => 'УК', 'payload' => [...], 'intent' => 'default', 'row' => 0], ...]
     */
    public function sendMessageWithInlineKeyboard(int $userId, ?string $text, array $buttons): DomainSendMessageResult
    {
        $message = new NewMessageBody();

        if ($text !== null && $text !== '') {
            $text = $this->truncateText($text);
            try {
                $message->setText($text);
            } catch (MaxLengthException $e) {
                $this->logger->warning("MaxBot: MaxLengthException в sendMessageWithInlineKeyboard — fallback обрезка текста");
                $text = $this->forceTruncateText($text);
                $message->setText($text);
            }
        }

        $keyboard = $message->addInlineKeyboard();

        // Группируем кнопки по рядам (по полю 'row' или последовательно)
        $rows = $this->groupButtonsByRow($buttons);

        foreach ($rows as $rowIndex => $rowButtons) {
            if ($rowIndex > 0) {
                $keyboard->newRow();
            }
            foreach ($rowButtons as $btn) {
                $intent = $this->resolveIntent($btn['intent'] ?? 'default');
                $payload = $btn['payload'] ?? [];
                $keyboard->addCallbackButton($btn['text'], $payload, $intent);
            }
        }

        $this->logger->debug("MaxBot: sendMessageWithInlineKeyboard userId={$userId}, buttons=" . count($buttons));

        $result = $this->client->sendMessageToUser($userId, $message, false);

        return $this->mapResult($result);
    }

    public function answerCallbackWithMessage(string $callbackId, string $text, ?array $inlineButtons = null): void
    {
        $text = $this->truncateText($text);

        try {
            $message = new NewMessageBody($text);
        } catch (MaxLengthException $e) {
            $this->logger->warning("MaxBot: MaxLengthException в answerCallbackWithMessage — fallback обрезка текста");
            $text = $this->forceTruncateText($text);
            $message = new NewMessageBody($text);
        }

        if ($inlineButtons !== null) {
            $keyboard = $message->addInlineKeyboard();
            $rows = $this->groupButtonsByRow($inlineButtons);

            foreach ($rows as $rowIndex => $rowButtons) {
                if ($rowIndex > 0) {
                    $keyboard->newRow();
                }
                foreach ($rowButtons as $btn) {
                    $intent = $this->resolveIntent($btn['intent'] ?? 'default');
                    $payload = $btn['payload'] ?? [];
                    $keyboard->addCallbackButton($btn['text'], $payload, $intent);
                }
            }
        }

        $answer = new CallbackAnswer($message, null);
        $this->client->answerOnCallback($callbackId, $answer);

        $this->logger->debug("MaxBot: answerCallbackWithMessage callbackId={$callbackId}");
    }

    public function answerCallbackNotification(string $callbackId, string $notification): void
    {
        $answer = new CallbackAnswer(null, $notification);
        $this->client->answerOnCallback($callbackId, $answer);

        $this->logger->debug("MaxBot: answerCallbackNotification callbackId={$callbackId}");
    }

    public function sendAction(int $chatId, string $action): void
    {
        $senderAction = SenderAction::from($action);
        $this->client->sendAction($chatId, $senderAction);

        $this->logger->debug("MaxBot: sendAction chatId={$chatId}, action={$action}");
    }

    /**
     * Маппинг библиотечного SendMessageResult в Domain Value Object
     */
    private function mapResult(LibSendMessageResult $result): DomainSendMessageResult
    {
        try {
            $message = $result->getMessage();
            $body = $message->getBody();
            $messageId = $body->getMid();
            $timestamp = intdiv($message->getTimestampRaw(), 1000);
        } catch (Throwable $e) {
            $this->logger->warning("MaxBot: не удалось извлечь данные из SendMessageResult", [
                'exception' => $e->getMessage(),
            ]);
            return DomainSendMessageResult::empty();
        }

        return new DomainSendMessageResult($messageId, $timestamp);
    }

    /**
     * Группирует кнопки по рядам (по полю 'row')
     * Если 'row' не указан — каждая кнопка в отдельном ряду
     *
     * @param array<array{text: string, payload: array, intent?: string, row?: int}> $buttons
     * @return array<int, array>
     */
    private function groupButtonsByRow(array $buttons): array
    {
        $rows = [];
        $autoRowIndex = 0;

        foreach ($buttons as $btn) {
            $row = $btn['row'] ?? $autoRowIndex;
            if (!isset($rows[$row])) {
                $rows[$row] = [];
            }
            $rows[$row][] = $btn;
            $autoRowIndex++;
        }

        ksort($rows);
        return array_values($rows);
    }

    /**
     * Преобразует строковый intent в enum Intent
     */
    private function resolveIntent(string $intent): Intent
    {
        return match ($intent) {
            'positive' => Intent::Positive,
            'negative' => Intent::Negative,
            default => Intent::Default,
        };
    }

    /**
     * Обрезает текст до лимита Max API (4000 символов) с пометкой «...текст сокращён»
     */
    private function truncateText(string $text): string
    {
        if (mb_strlen($text) <= self::MAX_TEXT_LENGTH) {
            return $text;
        }

        $suffixLen = mb_strlen(self::TRUNCATE_SUFFIX);
        return mb_substr($text, 0, self::MAX_TEXT_LENGTH - $suffixLen) . self::TRUNCATE_SUFFIX;
    }

    /**
     * Принудительная обрезка текста — fallback при MaxLengthException
     * Гарантированно обрезает текст до MAX_TEXT_LENGTH символов с суффиксом
     */
    private function forceTruncateText(string $text): string
    {
        $suffixLen = mb_strlen(self::TRUNCATE_SUFFIX);
        $maxContentLen = self::MAX_TEXT_LENGTH - $suffixLen;

        if (mb_strlen($text) <= $maxContentLen) {
            return $text . self::TRUNCATE_SUFFIX;
        }

        return mb_substr($text, 0, $maxContentLen) . self::TRUNCATE_SUFFIX;
    }
}
