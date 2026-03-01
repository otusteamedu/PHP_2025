<?php

declare(strict_types=1);

namespace App\Infrastructure\MessageQueue;

use App\Domain\Interfaces\MessageConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQConsumer implements MessageConsumerInterface
{
    public function __construct(
        private readonly RabbitMQConnection $connection
    ) {}

    public function consume(string $queue, callable $callback): void
    {
        $this->connection->declareQueue($queue);

        $channel = $this->connection->getChannel();

        $channel->basic_qos(0, 1, false);

        $channel->basic_consume(
            $queue,
            '',
            false,
            false,
            false,
            false,
            function (AMQPMessage $message) use ($callback) {
                $data = json_decode($message->getBody(), true);

                try {
                    $callback($data);
                    $message->ack();
                } catch (\Throwable $e) {
                    echo "Error processing message: " . $e->getMessage() . "\n";
                    $message->nack(true);
                }
            }
        );

        while ($channel->is_consuming()) {
            $channel->wait();
        }
    }

    public function get(string $queue): ?array
    {
        $this->connection->declareQueue($queue);

        $message = $this->connection->getChannel()->basic_get($queue);

        if ($message === null) {
            return null;
        }

        $data = json_decode($message->getBody(), true);
        $message->ack();

        return $data;
    }
}
