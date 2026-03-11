<?php
declare(strict_types=1);

namespace App\Domain\Task\Interface;

use App\Domain\Task\ValueObject\TaskId;

interface TaskPublisherInterface
{
    public function publish(TaskId $taskId): void;
}
