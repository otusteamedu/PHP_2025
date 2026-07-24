<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Domain\Entity\Task;
use App\Domain\Enum\TaskStatus;
use App\Domain\Exception\AppException;
use App\Domain\Repository\TaskRepositoryInterface;

final readonly class ProcessTaskUseCase
{
    public function __construct(
        private TaskRepositoryInterface $repository,
    )
    {
    }

    /**
     * @throws AppException
     */
    public function execute(Task $task): void
    {
        $task->changeStatus(TaskStatus::Processing);

        $this->repository->save($task);

        sleep(5);

        if (random_int(0, 1) === 1) {
            $task->changeStatus(TaskStatus::Completed);
        } else {
            $task->changeStatus(TaskStatus::Failed);
        }

        $this->repository->save($task);
    }
}