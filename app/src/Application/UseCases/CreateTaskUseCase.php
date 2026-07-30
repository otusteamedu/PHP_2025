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

    public function execute(array $data): Task
    {
        $task = new Task(data: $data);

        $this->repository->save($task);

        return $task;
    }
}
