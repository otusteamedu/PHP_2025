<?php

declare(strict_types=1);

namespace App\Infrastructure\RabbitMq\Connection;

use App\Infrastructure\RabbitMq\Exception\AmqpConnectionException;
use PhpAmqpLib\Channel\AMQPChannel;

interface AmqpConnectionInterface
{
    /**
     * @throws AmqpConnectionException
     */
    public function createChannel(): AMQPChannel;

    /**
     * @throws AmqpConnectionException
     */
    public function withChannel(callable $callback): void;
}
