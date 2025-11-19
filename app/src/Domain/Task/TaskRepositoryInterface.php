<?php

declare(strict_types=1);

namespace App\Domain\Task;

use Ramsey\Uuid\Uuid;

interface TaskRepositoryInterface
{
    public function persist(Task $task): void;

    public function flush(): void;

    public function findById(string $id): ?Task;

}
