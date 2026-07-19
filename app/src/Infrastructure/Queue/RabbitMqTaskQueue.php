<?php

declare(strict_types=1);

namespace App\Infrastructure\Queue;

use App\Domain\Entity\Task;
use App\Domain\Handler\TaskHandlerInterface;
use App\Domain\Queue\TaskQueueInterface;
use JsonException;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

final class RabbitMqTaskQueue implements TaskQueueInterface
{
    private const QUEUE_NAME = 'tasks';
    private const EXCHANGE_NAME = 'tasks.exchange';
    private const ROUTING_KEY = 'exchange.routing';

    private readonly AMQPStreamConnection $connection;

    /**
     * @throws \Exception
     */
    public function __construct(
        string $host,
        int $port,
        string $user,
        string $password,
    ) {
        $this->connection = new AMQPStreamConnection(
            host: $host,
            port: $port,
            user: $user,
            password: $password,
        );
    }

    /**
     * @throws JsonException
     */
    public function push(Task $task): void
    {
        $channel = $this->connection->channel();

        try {

            $this->declareExchange($channel);
            $this->declareQueue($channel);
            $this->bindQueue($channel);

            $message = new AMQPMessage(
                json_encode(
                    $task->toArray(),
                    flags: JSON_THROW_ON_ERROR,
                ),
                [
                    'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
                ]
            );

            $channel->basic_publish(
                $message,
                self::EXCHANGE_NAME,
                self::ROUTING_KEY
            );
        } finally {
            $channel->close();
        }
    }

    /**
     * @throws JsonException
     */
    public function consume(TaskHandlerInterface $handler): void
    {
        $channel = $this->connection->channel();

        $this->declareExchange($channel);
        $this->declareQueue($channel);
        $this->bindQueue($channel);

        $channel->basic_qos(
            prefetch_size: 0,
            prefetch_count: 1,
            a_global: false
        );

        $channel->basic_consume(
            queue: self::QUEUE_NAME,
            callback: function (AMQPMessage $message) use ($handler): void {
                $task = Task::fromArray(
                    json_decode(
                        $message->getBody(),
                        true,
                        flags: JSON_THROW_ON_ERROR
                    )
                );

                $handler->handle($task);

                $message->ack();
            }
        );

        while ($channel->is_consuming()) {
            $channel->wait();
        }
    }

    private function declareQueue(AMQPChannel $channel): void
    {
        $channel->queue_declare(
            queue: self::QUEUE_NAME,
            durable: true,
            auto_delete: false,
        );
    }

    private function declareExchange(AMQPChannel $channel): void
    {
        $channel->exchange_declare(
            exchange: self::EXCHANGE_NAME,
            type: 'direct',
            durable: true,
            auto_delete: false,
        );
    }

    private function bindQueue(AMQPChannel $channel): void
    {
        $channel->queue_bind(
            self::QUEUE_NAME,
            self::EXCHANGE_NAME,
            self::ROUTING_KEY
        );
    }

    /**
     * @throws \Exception
     */
    public function __destruct()
    {
        $this->connection->close();
    }
}