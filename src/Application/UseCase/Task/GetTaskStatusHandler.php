<?php
declare(strict_types=1);

namespace App\Application\UseCase\Task;

use App\Domain\Exception\NotFoundException;
use App\Domain\Task\Entity\Task;
use App\Domain\Task\Interface\TaskRepositoryInterface;
use App\Domain\Task\ValueObject\TaskId;

class GetTaskStatusHandler
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
    ) {}

    public function handle(TaskId $taskId): Task
    {
        $task = $this->taskRepository->findById($taskId);
        if ($task === null) {
            throw new NotFoundException('Task not found');
        }

        return $task;
    }
}
