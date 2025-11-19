<?php

declare(strict_types=1);

namespace App\Application\CreateTask;

use App\Domain\Task\Status;
use App\Domain\Task\Task;
use App\Domain\Task\TaskRepositoryInterface;
use App\UserInterface\Message\TaskMessage;
use Symfony\Component\Messenger\MessageBusInterface;
use Throwable;

class CreateTaskHandler
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
        private MessageBusInterface     $bus
    )
    {

    }

    public function __invoke(CreateTaskQuery $query): CreateTaskOutput
    {
        $task = new Task(
            email: $query->email,
            title: $query->title,
            description: $query->description,
        );

        $this->taskRepository->persist($task);

        try {
            $this->bus->dispatch(new TaskMessage($task->getId()));

            $task->updateStatus(Status::Waiting->value);
        } catch (Throwable) {

            $task->updateStatus(Status::Failed->value);
        }

        $this->taskRepository->flush();

        return new CreateTaskOutput($task->getId());
    }

}
