<?php

declare(strict_types=1);

namespace MkdBot\Infrastructure\Queue;

use AMQPConnection;

class RabbitMQConnectionFactory
{
    private ?AMQPConnection $connection = null;

    public function __construct(
        private readonly string $host,
        private readonly int $port,
        private readonly string $login,
        private readonly string $password,
        private readonly string $vhost,
    ) {
    }

    public function getConnection(): AMQPConnection
    {
        if ($this->connection === null) {
            $this->connection = new AMQPConnection([
                'host' => $this->host,
                'port' => $this->port,
                'login' => $this->login,
                'password' => $this->password,
                'vhost' => $this->vhost,
            ]);
        }

        return $this->connection;
    }

    /**
     * Создаёт новое подключение для health-check (каждый раз новое, без кэширования)
     */
    public function createFreshConnection(): AMQPConnection
    {
        return new AMQPConnection([
            'host' => $this->host,
            'port' => $this->port,
            'login' => $this->login,
            'password' => $this->password,
            'vhost' => $this->vhost,
        ]);
    }
}
