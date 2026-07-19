<?php

declare(strict_types=1);

namespace App\Application\Handler;

use App\Domain\Entity\Task;
use App\Domain\Handler\TaskHandlerInterface;

final class TaskConsoleHandler implements TaskHandlerInterface
{
    public function handle(Task $task): void
    {
        echo sprintf(
            "[%s] Statement request from %s\n",
            $task->createdAt()->format('Y-m-d H:i:s'),
            $task->email()
        );

        sleep(5);

        echo "Request has been processed\n";
    }
}