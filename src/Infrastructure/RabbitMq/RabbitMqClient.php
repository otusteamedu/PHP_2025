<?php
declare(strict_types=1);

namespace App\Infrastructure\RabbitMq;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitMqClient
{
    private AMQPStreamConnection $connection;
    private AMQPChannel $channel;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->connection = new AMQPStreamConnection(
            host: $_ENV['RABBITMQ_HOST'],
            port: (int) $_ENV['RABBITMQ_PORT'],
            user: $_ENV['RABBITMQ_USER'],
            password: $_ENV['RABBITMQ_PASS'],
        );

        $this->channel = $this->connection->channel();
    }

    public function __destruct()
    {
        $this->close();
    }

    public function getChannel(): AMQPChannel
    {
        return $this->channel;
    }

    public function close(): void
    {
        if (isset($this->channel) && $this->channel->is_open()) {
            $this->channel->close();
        }

        if (isset($this->connection) && $this->connection->isConnected()) {
            $this->connection->close();
        }
    }
}
