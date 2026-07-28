<?php

declare(strict_types=1);

namespace App\Domain\Queue;

use App\Domain\Entity\Task;
use App\Domain\Handler\TaskHandlerInterface;

interface TaskQueueInterface
{
    public function push(Task $task): void;

    public function consume(TaskHandlerInterface  $handler): void;
}
