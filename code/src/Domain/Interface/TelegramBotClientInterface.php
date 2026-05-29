<?php

declare(strict_types=1);

namespace MkdBot\Domain\Interface;

/**
 * Интерфейс клиента Telegram Bot API — только методы отправки сообщений
 * Без subscribe/getWebhookUpdate — они в Infrastructure-интерфейсе
 */
interface TelegramBotClientInterface
{
    /**
     * Отправка текстового сообщения
     * $params: chat_id, text, parse_mode, disable_web_page_preview и т.д.
     */
    public function sendMessage(array $params): mixed;

    /**
     * Отправка фото
     * $params: chat_id, photo (URL или InputFile), caption, parse_mode и т.д.
     */
    public function sendPhoto(array $params): mixed;

    /**
     * Отправка документа
     * $params: chat_id, document (InputFile), caption, parse_mode и т.д.
     */
    public function sendDocument(array $params): mixed;
}
