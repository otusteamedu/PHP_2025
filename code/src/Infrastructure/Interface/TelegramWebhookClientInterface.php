<?php

declare(strict_types=1);

namespace MkdBot\Infrastructure\Interface;

use Psr\Http\Message\RequestInterface;
use Telegram\Bot\Objects\Update as UpdateObject;

/**
 * Infrastructure-интерфейс для webhook- и long-polling-операций Telegram Bot API
 */
interface TelegramWebhookClientInterface
{
    /**
     * Устанавливает webhook URL для Telegram Bot API
     *
     * @param array $params Параметры: url, secret_token, allowed_updates и т.д.
     */
    public function setWebhook(array $params): bool;

    /**
     * Получает обновление webhook из входящего запроса
     *
     * @param RequestInterface|null $request HTTP-запрос (null = читать из php://input)
     */
    public function getWebhookUpdate(?RequestInterface $request = null): mixed;

    /**
     * Получает обновления через Long Polling (getUpdates)
     *
     * @param array $params Параметры: offset, limit, timeout, allowed_updates
     * @return UpdateObject[] Массив объектов обновлений
     */
    public function getUpdates(array $params = []): array;

    /**
     * Удаляет webhook — переключение на Long Polling
     */
    public function deleteWebhook(): bool;
}
