<?php

declare(strict_types=1);

namespace App\Infrastructure\RabbitMQ;


use PhpAmqpLib\Message\AMQPMessage;

final class RabbitMQConsumer
{
    private RabbitMQChannel $rabbitMQChannel;

    private AMQPMessage $message;
    private string $consumer_tag = 'consumer';

    public function __construct(RabbitMQChannel $rabbitMQChannel)
    {
        $this->rabbitMQChannel = $rabbitMQChannel;
    }

    public function receive(callable $callback): void
    {
        $channel = $this->rabbitMQChannel->getChannel();
        $options = $this->rabbitMQChannel->getOptions();
        $channel->basic_consume(
            $options['queue'],
            $this->consumer_tag,
            false,
            true,
            $options['exclusive'],
            false,
            $callback
        );

        while ($count = \count($channel->callbacks)) {
            $channel->wait();
        }

        $channel->close();
        $this->rabbitMQChannel->getAMQPStreamConnection()->close();
    }
}
