<?php

declare(strict_types=1);

namespace App\Infrastructure\MessageQueue;

use App\Domain\Interfaces\MessagePublisherInterface;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQPublisher implements MessagePublisherInterface
{
    public function __construct(
        private readonly RabbitMQConnection $connection
    ) {}

    public function publish(string $queue, array $message, string $exchange = '', string $routingKey = ''): void
    {
        $this->connection->declareQueue($queue);

        $amqpMessage = new AMQPMessage(
            json_encode($message, JSON_UNESCAPED_UNICODE),
            [
                'content_type' => 'application/json',
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
            ]
        );

        $this->connection->getChannel()->basic_publish(
            $amqpMessage,
            $exchange,
            $routingKey ?: $queue
        );
    }
}
