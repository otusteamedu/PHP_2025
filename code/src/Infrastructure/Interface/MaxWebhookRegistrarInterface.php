<?php

declare(strict_types=1);

namespace MkdBot\Infrastructure\Interface;

/**
 * Infrastructure-интерфейс для регистрации webhook в Max API
 */
interface MaxWebhookRegistrarInterface
{
    /**
     * Подписывает webhook URL на обновления Max API
     *
     * @param string $url URL для webhook
     * @param string $secret Секрет для проверки webhook (паттерн: /^[a-zA-Z0-9_-]{5,256}$/)
     * @param array<string> $updateTypes Типы обновлений для подписки
     */
    public function subscribe(string $url, string $secret, array $updateTypes): void;
}
