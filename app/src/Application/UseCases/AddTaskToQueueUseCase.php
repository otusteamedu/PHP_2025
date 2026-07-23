<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Domain\Entity\Task;
use App\Domain\Enum\TaskStatus;
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
    public function execute(Task $task): void
    {

        $this->queue->push($task);

        $task->changeStatus(TaskStatus::Queued);

        $this->repository->save($task);
    }
}