<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Domain\Entity\Task;
use App\Domain\Repository\TaskRepositoryInterface;

class GetTaskStatusUseCase
{
    public function __construct(
        private TaskRepositoryInterface $repository,
    )
    {

    }

    public function execute(int $taskNumber): ?string
    {
        $task = $this->repository->findByNumber($taskNumber);

        return $task?->getStatus()->value;
    }
}