<?php

declare(strict_types=1);

namespace App\Domain\Handler;

use App\Domain\Entity\Task;

interface TaskHandlerInterface
{
    public function handle(Task $task): void;
}