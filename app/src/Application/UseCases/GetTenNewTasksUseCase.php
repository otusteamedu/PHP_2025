<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Domain\Entity\Task;
use App\Domain\Repository\TaskRepositoryInterface;

class GetTenNewTasksUseCase
{
    public function __construct(
        private TaskRepositoryInterface $repository,
    )
    {

    }

    /**
     * @return Task[]
     */
    public function execute(): array
    {
        return $this->repository->getNew(10);
    }
}