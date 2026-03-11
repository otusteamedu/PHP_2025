<?php
declare(strict_types=1);

namespace App\Application\UseCase\Task;

use App\Domain\Exception\NotFoundException;
use App\Domain\Task\Enum\TaskStatus;
use App\Domain\Task\Interface\TaskRepositoryInterface;
use App\Domain\Task\ValueObject\TaskId;

class ProcessTaskHandler
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
    ) {}

    public function handle(TaskId $taskId): void
    {
        $task = $this->taskRepository->findById($taskId);
        if ($task === null) {
            throw new NotFoundException('Task not found');
        }

        if ($task->getStatus() === TaskStatus::Completed) {
            return;
        }

        $this->taskRepository->updateStatus($taskId, TaskStatus::Processing);

        sleep(30);

        $this->taskRepository->updateStatus($taskId, TaskStatus::Completed);
    }
}
