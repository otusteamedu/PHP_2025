<?php
declare(strict_types=1);

namespace App\Infrastructure\RabbitMq;

use Exception;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitMqConnectionFactory
{
    public function __construct(
        private readonly string $host,
        private readonly int $port,
        private readonly string $user,
        private readonly string $pass,
        private readonly string $vhost,
    ) {}

    /**
     * @throws Exception
     */
    public function create(): AMQPStreamConnection
    {
        return new AMQPStreamConnection(
            host: $this->host,
            port: $this->port,
            user: $this->user,
            password: $this->pass,
            vhost: $this->vhost
        );
    }
}
