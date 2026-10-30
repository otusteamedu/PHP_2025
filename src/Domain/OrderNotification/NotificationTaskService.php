<?php

declare(strict_types=1);

namespace App\Domain\OrderNotification;

use App\Domain\OrderNotification\Command\CreateNotificationTaskCommand;
use App\Domain\OrderNotification\Contract\NotificationTaskPublisherInterface;
use App\Domain\OrderNotification\Contract\NotificationTaskRepositoryInterface;
use App\Domain\OrderNotification\Entity\NotificationTask;
use App\Domain\OrderNotification\Exception\Task\NotificationTaskPublishingException;
use App\Domain\OrderNotification\Exception\Task\NotificationTaskStorageException;
use App\Domain\OrderNotification\Exception\Task\NotificationTaskNotFoundException;
use App\Domain\OrderNotification\ValueObject\Task\TaskId;

class NotificationTaskService
{
    public function __construct(
        private readonly NotificationTaskRepositoryInterface $taskRepository,
        private readonly NotificationTaskPublisherInterface $taskPublisher,
    ) {
    }

    /**
     * @throws NotificationTaskStorageException
     * @throws NotificationTaskPublishingException
     */
    public function createTask(CreateNotificationTaskCommand $command): NotificationTask
    {
        $task = NotificationTask::create(
            $command->orderId,
            $command->orderEvent,
            $command->channel,
            $command->recipient,
        );

        $this->taskRepository->persist($task);
        $this->taskPublisher->publish($task);

        return $task;
    }

    /**
     * @throws NotificationTaskStorageException
     * @throws NotificationTaskNotFoundException
     */
    public function getTask(TaskId $id): NotificationTask
    {
        $id = $id->value();

        $task = $this->taskRepository->find($id);
        if ($task === null) {
            throw NotificationTaskNotFoundException::byId($id);
        }

        return $task;
    }
}
