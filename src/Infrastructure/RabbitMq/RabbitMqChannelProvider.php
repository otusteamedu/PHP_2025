<?php
declare(strict_types=1);

namespace App\Infrastructure\RabbitMq;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitMqChannelProvider
{
    private ?AMQPStreamConnection $connection = null;
    private ?AMQPChannel $channel = null;

    public function __construct(
        private readonly RabbitMqConnectionFactory $factory
    ) {}

    /**
     * @return AMQPChannel
     * @throws \Throwable
     */
    public function channel(): AMQPChannel
    {
        if ($this->channel !== null) {
            return $this->channel;
        }

        try {
            $this->connection = $this->factory->create();
            $this->channel = $this->connection->channel();

            return $this->channel;
        } catch (\Throwable $exception) {
            $this->channel = null;
            $this->connection = null;
            throw $exception;
        }
    }

    public function close(): void
    {
        $this->channel?->close();
        $this->connection?->close();

        $this->channel = null;
        $this->connection = null;
    }
}
