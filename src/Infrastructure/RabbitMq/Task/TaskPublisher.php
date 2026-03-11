<?php
declare(strict_types=1);

namespace App\Infrastructure\RabbitMq\Task;

use App\Domain\Task\Interface\TaskPublisherInterface;
use App\Domain\Task\ValueObject\TaskId;
use App\Infrastructure\RabbitMq\AmqpMessageFactory;
use App\Infrastructure\RabbitMq\RabbitMqClient;

class TaskPublisher implements TaskPublisherInterface
{
    public const string QUEUE_NAME = 'task_queue';

    public function __construct(
        private RabbitMqClient $rabbitMq
    ) {}

    public function publish(TaskId $taskId): void
    {
        $channel = $this->rabbitMq->getChannel();

        $channel->queue_declare(
            queue: self::QUEUE_NAME,
            durable: true,
            auto_delete: false,
        );

        $channel->basic_publish(
            msg: AmqpMessageFactory::createMessage(json_encode(['task_id' => $taskId->toString()],JSON_THROW_ON_ERROR)),
            routing_key: self::QUEUE_NAME,
        );
    }
}
