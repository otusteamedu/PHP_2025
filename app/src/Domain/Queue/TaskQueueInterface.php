<?php

declare(strict_types=1);

namespace App\Domain\Queue;

use App\Domain\Entity\Task;

interface TaskQueueInterface
{
    public function push(Task $task): void;

    public function pop(): ?Task;
}