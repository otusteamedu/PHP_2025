<?php

declare(strict_types=1);

namespace App\Service;

use App\Enum\QueueNameEnum;
use App\Interface\QueueServiceInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitService implements QueueServiceInterface
{
    private AMQPStreamConnection $connection;

    public function __construct(
    ) {
        $this->connection = new AMQPStreamConnection(
            $_ENV['RABBIT_HOST'],
            $_ENV['RABBIT_PORT'],
            $_ENV['RABBIT_USER'],
            $_ENV['RABBIT_PASSWORD']
        );
    }

    public function publish(QueueNameEnum $queueName, string $message): void
    {
        $channel = $this->connection->channel();
        $channel->queue_declare($queueName->value, false, false, false, false);

        $msg = new AMQPMessage($message);
        $channel->basic_publish($msg, '', $queueName->value);
    }

    public function consume(QueueNameEnum $queueName, callable $callback): void
    {
        $channel = $this->connection->channel();
        $channel->queue_declare($queueName->value, false, false, false, false);
        $channel->basic_consume($queueName->value, '', false, true, false, false, $callback);

        while ($channel->is_consuming()) {
            $channel->wait();
        }
    }
}
