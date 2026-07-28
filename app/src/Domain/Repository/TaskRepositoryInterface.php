<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Task;

interface TaskRepositoryInterface
{
    public function findByNumber(int $number): ?Task;

    public function save(Task $task): void;

    /**
     * @return Task[]
     */
    public function getNew(int $limit): array;
}
