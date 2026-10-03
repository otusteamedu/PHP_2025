<?php

declare(strict_types=1);

namespace App\Infrastructure\RabbitMq\Connection;

use App\Infrastructure\RabbitMq\Exception\AmqpConnectionException;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class AmqpConnection implements AmqpConnectionInterface
{
    public function __construct(
        private readonly AMQPStreamConnection $connection,
    ) {
    }

    public function createChannel(): AMQPChannel
    {
        try {
            return $this->connection->channel();
        } catch (\Throwable $e) {
            throw new AmqpConnectionException(
                'Failed to create channel: ' . $e->getMessage(),
                previous: $e,
            );
        }
    }

    public function withChannel(callable $callback): void
    {
        $channel = $this->createChannel();

        try {
            $callback($channel);
        } catch (\Throwable $e) {
            throw new AmqpConnectionException(
                'Channel operation failed: ' . $e->getMessage(),
                previous: $e,
            );
        } finally {
            $channel->close();
        }
    }
}
