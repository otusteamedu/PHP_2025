<?php

declare(strict_types=1);

namespace Infrastructure\Rabbit;

use Exception;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class Connection
{
    protected AMQPStreamConnection $connection;
    private AMQPChannel $channel;

    /**
     * @throws Exception
     */
    public function __construct(array $env)
    {
        $this->connection = new AMQPStreamConnection(
            $env['RABBITMQ_HOST'],
            $env['RABBITMQ_PORT'],
            $env['RABBITMQ_USER'],
            $env['RABBITMQ_PASSWORD']
        );

        $this->channel = $this->connection->channel();

        $this->channel->queue_declare(
            $env['RABBITMQ_QUEUE'],
            false,
            true,
            false,
            false
        );
    }

    public function getChannel(): AMQPChannel
    {
        return $this->channel;
    }

    /**
     * @throws Exception
     */
    public function close(): void
    {
        $this->connection->close();
    }
}