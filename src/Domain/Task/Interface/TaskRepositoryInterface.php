<?php
declare(strict_types=1);

namespace App\Domain\Task\Interface;

use App\Domain\Task\Entity\Task;
use App\Domain\Task\Enum\TaskStatus;
use App\Domain\Task\ValueObject\TaskId;

interface TaskRepositoryInterface
{
    public function save(Task $task): void;

    public function findById(TaskId $id): ?Task;

    public function updateStatus(TaskId $id, TaskStatus $status): void;
}
