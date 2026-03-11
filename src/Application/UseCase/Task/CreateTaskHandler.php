<?php
declare(strict_types=1);

namespace App\Application\UseCase\Task;

use App\Domain\Task\Entity\Task;
use App\Domain\Task\Enum\TaskStatus;
use App\Domain\Task\Interface\TaskPublisherInterface;
use App\Domain\Task\Interface\TaskRepositoryInterface;
use App\Domain\Task\ValueObject\TaskId;
use DateTimeImmutable;
use DateTimeZone;
use Ramsey\Uuid\Uuid;

class CreateTaskHandler
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
        private TaskPublisherInterface $taskPublisher,
    ) {}

    public function handle(array $payload): Task
    {
        $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));

        $task = new Task(
            id: new TaskId(Uuid::uuid7()->toString()),
            status: TaskStatus::Queued,
            payload: $payload,
            createdAt: $now,
            updatedAt: $now,
        );

        $this->taskRepository->save($task);
        $this->taskPublisher->publish($task->getId());

        return $task;
    }
}
