<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Domain\Entity\Task;
use App\Domain\Repository\TaskRepositoryInterface;

readonly class CreateTaskUseCase
{
    public function __construct(
        private TaskRepositoryInterface $repository
    )
    {
    }

    public function execute(): Task
    {
        $task = new Task(0);

        $this->repository->save($task);

        return $task;
    }
}