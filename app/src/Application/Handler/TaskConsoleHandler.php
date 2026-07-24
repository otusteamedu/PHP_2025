<?php

declare(strict_types=1);

namespace App\Application\Handler;

use App\Application\UseCases\ProcessTaskUseCase;
use App\Domain\Entity\Task;
use App\Domain\Handler\TaskHandlerInterface;

final class TaskConsoleHandler implements TaskHandlerInterface
{
    public function __construct(
        private ProcessTaskUseCase $useCase,
    )
    {
    }

    public function handle(Task $task): void
    {
        echo "Processing task №{$task->getNumber()}\n";

        $this->useCase->execute($task);

        echo "Task №{$task->getNumber()} finished with status {$task->getStatus()->value}\n";
    }
}