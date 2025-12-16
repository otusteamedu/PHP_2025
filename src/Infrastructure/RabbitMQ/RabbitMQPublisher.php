<?php

declare(strict_types=1);

namespace App\Infrastructure\RabbitMQ;


use PhpAmqpLib\Message\AMQPMessage;

final class RabbitMQPublisher
{
    private RabbitMQChannel $rabbitMQChannel;

    private AMQPMessage $message;

    public function __construct(RabbitMQChannel $rabbitMQChannel)
    {
        $this->rabbitMQChannel = $rabbitMQChannel;
    }

    public function send(string $message): void
    {
        $msg = new AMQPMessage($message);

        $options = $this->rabbitMQChannel->getOptions();
        $channel = $this->rabbitMQChannel->getChannel();

        $channel->basic_publish($msg, $options['exchange'], $options['queue']);

        $channel->close();
        $this->rabbitMQChannel->getAMQPStreamConnection()->close();
    }
}
