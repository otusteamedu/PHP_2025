<?php

declare(strict_types=1);

namespace App\Application\ProcessTaskMessage;

use App\Domain\Task\Status;
use App\Domain\Task\TaskRepositoryInterface;

class ProcessTaskMessageHandler
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
    )
    {
    }

    public function __invoke(ProcessTaskMessageQuery $query): void
    {
        $task = $this->taskRepository->findById($query->id);

        $task->updateStatus(Status::Done->value);

        $this->taskRepository->persist($task);
        $this->taskRepository->flush();
    }

}
