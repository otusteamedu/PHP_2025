<?php

declare(strict_types=1);

namespace MkdBot\Domain\Interface;

use MkdBot\Domain\ValueObject\SendMessageResult;

/**
 * Интерфейс клиента Max Bot API — абстракция для лёгкой замены библиотеки
 */
interface MaxBotClientInterface
{
    /**
     * Отправка простого текстового сообщения пользователю
     */
    public function sendMessageToUser(int $userId, string $text, bool $disableLinkPreview = false): SendMessageResult;

    /**
     * Отправка простого текстового сообщения в чат
     */
    public function sendMessageToChat(int $chatId, string $text, bool $disableLinkPreview = false): SendMessageResult;

    /**
     * Отправка сообщения с inline-клавиатурой
     * $text — опционален (NewMessageBody позволяет отправить только клавиатуру)
     * $buttons — массив кнопок: [['text' => 'УК', 'payload' => ['action' => 'contacts', 'type' => 'uk'], 'intent' => 'default'], ...]
     * Каждая кнопка может содержать 'row' => int для группировки по рядам
     */
    public function sendMessageWithInlineKeyboard(int $userId, ?string $text, array $buttons): SendMessageResult;

    /**
     * Ответ на callback с обновлением сообщения (inline-кнопка обновляется)
     * $inlineButtons — опционально: новая inline-клавиатура для обновлённого сообщения
     */
    public function answerCallbackWithMessage(string $callbackId, string $text, ?array $inlineButtons = null): void;

    /**
     * Ответ на callback с одноразовым уведомлением (не обновляет сообщение)
     */
    public function answerCallbackNotification(string $callbackId, string $notification): void;

    /**
     * Отправка действия бота (например, «печатает»)
     * $action — строка, преобразуемая в SenderAction: typing_on, sending_photo и т.д.
     */
    public function sendAction(int $chatId, string $action): void;
}
