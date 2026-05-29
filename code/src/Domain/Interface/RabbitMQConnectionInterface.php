<?php

declare(strict_types=1);

namespace MkdBot\Domain\Interface;

/**
 * Интерфейс проверки доступности RabbitMQ — абстракция для v2 миграции на Managed Queue
 */
interface RabbitMQConnectionInterface
{
    /**
     * Проверяет доступность подключения к RabbitMQ
     */
    public function isAvailable(): bool;
}
