<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Domain\Entity\Task;
use App\Domain\Enum\TaskStatus;
use App\Domain\Exception\AppException;
use App\Domain\Queue\TaskQueueInterface;
use App\Domain\Repository\TaskRepositoryInterface;

class AddTaskToQueueUseCase
{
    public function __construct(
        private TaskRepositoryInterface $repository,
        private TaskQueueInterface $queue,
    )
    {

    }

    /**
     * @throws AppException
     */
    public function execute(Task $task): void
    {
        $task->changeStatus(TaskStatus::Queued);

        $this->repository->save($task);

        $this->queue->push($task);
    }
}
