<?php

declare(strict_types=1);

namespace App\Infrastructure\MessageQueue;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Channel\AMQPChannel;

class RabbitMQConnection
{
    private ?AMQPStreamConnection $connection = null;
    private ?AMQPChannel $channel = null;

    public function __construct(
        private readonly string $host = 'rabbitmq',
        private readonly int $port = 5672,
        private readonly string $user = 'guest',
        private readonly string $password = 'guest',
        private readonly string $vhost = '/'
    ) {}

    public function getConnection(): AMQPStreamConnection
    {
        if ($this->connection === null || !$this->connection->isConnected()) {
            $this->connection = new AMQPStreamConnection(
                $this->host,
                $this->port,
                $this->user,
                $this->password,
                $this->vhost
            );
        }

        return $this->connection;
    }

    public function getChannel(): AMQPChannel
    {
        if ($this->channel === null || !$this->channel->is_open()) {
            $this->channel = $this->getConnection()->channel();
        }

        return $this->channel;
    }

    public function declareQueue(string $queue, bool $durable = true, bool $autoDelete = false): void
    {
        $this->getChannel()->queue_declare(
            $queue,
            false,
            $durable,
            false,
            $autoDelete
        );
    }

    public function declareExchange(string $exchange, string $type = 'direct', bool $durable = true): void
    {
        $this->getChannel()->exchange_declare(
            $exchange,
            $type,
            false,
            $durable
        );
    }

    public function bindQueue(string $queue, string $exchange, string $routingKey = ''): void
    {
        $this->getChannel()->queue_bind($queue, $exchange, $routingKey);
    }

    public function close(): void
    {
        if ($this->channel !== null && $this->channel->is_open()) {
            $this->channel->close();
        }

        if ($this->connection !== null && $this->connection->isConnected()) {
            $this->connection->close();
        }
    }

    public function __destruct()
    {
        $this->close();
    }
}
