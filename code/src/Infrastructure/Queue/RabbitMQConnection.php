<?php

declare(strict_types=1);

namespace MkdBot\Infrastructure\Queue;

use AMQPConnection;
use AMQPException;
use MkdBot\Domain\Interface\RabbitMQConnectionInterface;

/**
 * Проверка доступности RabbitMQ через попытку подключения
 */
class RabbitMQConnection implements RabbitMQConnectionInterface
{
    public function __construct(
        private readonly string $host,
        private readonly int $port,
        private readonly string $login,
        private readonly string $password,
        private readonly string $vhost,
    ) {
    }

    public function isAvailable(): bool
    {
        try {
            $connection = new AMQPConnection([
                'host' => $this->host,
                'port' => $this->port,
                'login' => $this->login,
                'password' => $this->password,
                'vhost' => $this->vhost,
            ]);
            $connection->connect();
            $connected = $connection->isConnected();
            $connection->disconnect();

            return $connected;
        } catch (AMQPException) {
            return false;
        }
    }
}
