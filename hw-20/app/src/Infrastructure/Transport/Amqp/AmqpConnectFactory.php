<?php

declare(strict_types=1);

namespace App\Infrastructure\Transport\Amqp;

use AMQPConnection;

final readonly class AmqpConnectFactory
{
    public function __construct(
        private string $host,
        private string $port,
        private string $login,
        private string $password,
        private string $vhost,
    ) {
    }

    public function create(): AMQPConnection
    {
        $conn = new AMQPConnection([
            'host' => $this->host,
            'port' => (int) $this->port,
            'login' => $this->login,
            'password' => $this->password,
            'vhost' => $this->vhost,
        ]);
        $conn->connect();

        return $conn;
    }
}