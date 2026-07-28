<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Repository;

use App\Domain\Entity\Task;
use App\Domain\Exception\AppException;
use App\Domain\Repository\TaskRepositoryInterface;
use App\Infrastructure\Database\TaskDataMapper;

final readonly class TaskRepository implements TaskRepositoryInterface
{
    public function __construct(
        private TaskDataMapper $mapper,
    )
    {
    }

    public function findByNumber(int $number): ?Task
    {
        return $this->mapper->findByNumber(
            $number
        );
    }

    /**
     * @throws AppException
     */
    public function save(Task $task): void
    {
        if ($task->getNumber() === 0) {
            $this->mapper->insert($task);

            return;
        }

        $this->mapper->update($task);
    }

    /**
     * @return Task[]
     */
    public function getNew(int $limit): array
    {
        return $this->mapper->getNewTasks($limit);
    }
}
