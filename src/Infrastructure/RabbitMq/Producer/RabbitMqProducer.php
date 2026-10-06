<?php

declare(strict_types=1);

namespace App\Infrastructure\RabbitMq\Producer;

use App\Infrastructure\RabbitMq\Connection\AmqpConnectionInterface;
use App\Infrastructure\RabbitMq\Exception\AmqpConnectionException;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMqProducer
{
    public function __construct(
        private readonly AmqpConnectionInterface $connection,
    ) {
    }

    /**
     * @throws AmqpConnectionException
     */
    public function publish(string $body, string $exchange = '', string $routingKey = ''): void
    {
        $this->connection->withChannel(function (AMQPChannel $channel) use ($body, $exchange, $routingKey) {
            $channel->basic_publish(
                msg: new AMQPMessage(
                    $body,
                    ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT],
                ),
                exchange: $exchange,
                routing_key: $routingKey,
            );
        });
    }
}
