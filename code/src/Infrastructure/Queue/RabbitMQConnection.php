<?php

declare(strict_types=1);

namespace MkdBot\Infrastructure\Queue;

use AMQPException;
use MkdBot\Domain\Interface\RabbitMQConnectionInterface;

/**
 * Проверка доступности RabbitMQ через попытку подключения
 * Использует RabbitMQConnectionFactory для создания свежего подключения при каждой проверке
 */
class RabbitMQConnection implements RabbitMQConnectionInterface
{
    public function __construct(
        private readonly RabbitMQConnectionFactory $connectionFactory,
    ) {
    }

    public function isAvailable(): bool
    {
        try {
            $connection = $this->connectionFactory->createFreshConnection();
            $connection->connect();
            $connected = $connection->isConnected();
            $connection->disconnect();

            return $connected;
        } catch (AMQPException) {
            return false;
        }
    }
}
