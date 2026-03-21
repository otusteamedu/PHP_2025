<?php

namespace Ak\Hw\Infrastructure\Messaging;

use Ak\Hw\Domain\Messaging\QueueProducerInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMqProducer implements QueueProducerInterface
{
    private AMQPStreamConnection $connection;
    private \PhpAmqpLib\Channel\AMQPChannel $channel;

    public function __construct(string $host, int $port, string $user, string $password)
    {
        $this->connection = new AMQPStreamConnection(
            $host, $port, $user, $password
        );
        $this->channel = $this->connection->channel();
    }

    /**
     * @throws \JsonException
     */
    public function publish(string $queueName, array $messageBody): void
    {
        $this->channel->queue_declare($queueName, false, true, false, false);

        $msg = new AMQPMessage(
            json_encode($messageBody, JSON_THROW_ON_ERROR),
            ['delivery_mode' => 2] // 2 = persistent
        );
        $this->channel->basic_publish($msg, '', $queueName);
    }

    public function close(): void
    {
        $this->channel->close();
        $this->connection->close();
    }
}