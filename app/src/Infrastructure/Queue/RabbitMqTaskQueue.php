<?php

declare(strict_types=1);

namespace App\Infrastructure\Queue;

use App\Domain\Entity\Task;
use App\Domain\Handler\TaskHandlerInterface;
use App\Domain\Queue\TaskQueueInterface;
use App\Infrastructure\Queue\Config\RabbitMqConfig;
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
    private readonly AMQPChannel $channel;

    /**
     * @throws \Exception
     */
    public function __construct(
        RabbitMqConfig $config
    )
    {
        $this->connection = new AMQPStreamConnection(
            host: $config->host,
            port: $config->port,
            user: $config->user,
            password: $config->password,
        );
        $this->channel = $this->connection->channel();

        $this->declareExchange($this->channel);
        $this->declareQueue($this->channel);
        $this->bindQueue($this->channel);

    }

    /**
     * @throws JsonException
     */
    public function push(Task $task): void
    {
        $message = new AMQPMessage(
            json_encode(
                $task->toArray(),
                flags: JSON_THROW_ON_ERROR,
            ),
            [
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
            ]
        );

        $this->channel->basic_publish(
            $message,
            self::EXCHANGE_NAME,
            self::ROUTING_KEY
        );
    }

    /**
     * @throws JsonException
     */
    public function consume(TaskHandlerInterface $handler): void
    {
        $this->channel->basic_qos(
            prefetch_size: 0,
            prefetch_count: 1,
            a_global: false
        );

        $this->channel->basic_consume(
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

        while ($this->channel->is_consuming()) {
            $this->channel->wait();
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
    public function close(): void
    {
        if ($this->connection->isConnected()) {
            $this->connection->close();
        }
    }

}
