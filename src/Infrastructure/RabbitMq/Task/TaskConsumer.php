<?php
declare(strict_types=1);

namespace App\Infrastructure\RabbitMq\Task;

use App\Application\UseCase\Task\ProcessTaskHandler;
use App\Domain\Exception\InvalidQueueMessageException;
use App\Domain\Exception\RetryableProcessingException;
use App\Domain\Task\ValueObject\TaskId;
use App\Infrastructure\RabbitMq\RabbitMqClient;
use PhpAmqpLib\Message\AMQPMessage;

class TaskConsumer
{
    public function __construct(
        private RabbitMqClient $rabbitMq,
        private ProcessTaskHandler $processTaskHandler
    ) {}

    public function consume(): void
    {
        $channel = $this->rabbitMq->getChannel();

        $channel->queue_declare(
            queue: TaskPublisher::QUEUE_NAME,
            durable: true,
            auto_delete: false,
        );

        $channel->basic_qos(0, 1, false);

        $channel->basic_consume(
            queue: TaskPublisher::QUEUE_NAME,
            callback: function (AMQPMessage $message): void {
                try {
                    $taskId = $this->extractTaskId($message);
                    $this->processTaskHandler->handle($taskId);
                    $message->ack();
                } catch (RetryableProcessingException) {
                    $message->nack(false, true);
                } catch (\Throwable $exception) {
                    $message->nack(false, true);
                    throw $exception;
                }
            }
        );

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $this->rabbitMq->close();
    }

    private function extractTaskId(AMQPMessage $message): TaskId
    {
        $payload = json_decode($message->getBody(), true, 512, JSON_THROW_ON_ERROR);

        if (!is_array($payload) || !array_key_exists('task_id', $payload)) {
            throw new InvalidQueueMessageException('Queue message must contain task_id');
        }

        return new TaskId((string) $payload['task_id']);
    }
}
