<?php

declare(strict_types=1);

namespace App\Domain\OrderNotification;

use App\Domain\OrderNotification\Channel\NotificationHandlerFactory;
use App\Domain\OrderNotification\Command\CreateNotificationTaskCommand;
use App\Domain\OrderNotification\Contract\NotificationTaskPublisherInterface;
use App\Domain\OrderNotification\Contract\NotificationTaskRepositoryInterface;
use App\Domain\OrderNotification\Contract\OrderDataProviderInterface;
use App\Domain\OrderNotification\Entity\NotificationTask;
use App\Domain\OrderNotification\Exception\Delivery\MaxAttemptsExceededException;
use App\Domain\OrderNotification\Exception\Delivery\NotificationSendingException;
use App\Domain\OrderNotification\Exception\OrderSource\OrderDataInvalidException;
use App\Domain\OrderNotification\Exception\OrderSource\OrderDataProvidingException;
use App\Domain\OrderNotification\Exception\Task\InvalidTaskStateException;
use App\Domain\OrderNotification\Exception\Task\NotificationTaskPublishingException;
use App\Domain\OrderNotification\Exception\Task\NotificationTaskStorageException;
use App\Domain\OrderNotification\Exception\Task\NotificationTaskNotFoundException;
use App\Domain\OrderNotification\ValueObject\Task\TaskId;
use App\Domain\OrderNotification\ValueObject\Task\TaskStatus;

class NotificationTaskService
{
    public function __construct(
        private readonly NotificationTaskRepositoryInterface $taskRepository,
        private readonly NotificationTaskPublisherInterface $taskPublisher,
        private readonly OrderDataProviderInterface $orderDataProvider,
        private readonly NotificationHandlerFactory $handlerFactory,
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

    /**
     * @throws InvalidTaskStateException
     * @throws MaxAttemptsExceededException
     * @throws NotificationTaskStorageException
     * @throws NotificationSendingException
     * @throws OrderDataInvalidException
     * @throws OrderDataProvidingException
     */
    public function processTask(NotificationTask $task, int $maxAttempts): void
    {
        $task->beginAttempt($maxAttempts);

        if ($task->getStatus() === TaskStatus::Pending) {
            $this->taskRepository->transitionTo($task, TaskStatus::Fetching);
        } else {
            $this->taskRepository->restart($task);
        }

        $this->loadOrderData($task);
        $this->taskRepository->transitionTo($task, TaskStatus::Sending);

        $this->dispatchNotification($task);
        $this->taskRepository->transitionTo($task, TaskStatus::Completed);
    }

    /**
     * @throws InvalidTaskStateException
     * @throws NotificationTaskStorageException
     */
    public function failTask(NotificationTask $task, string $reason): void
    {
        $this->taskRepository->fail($task, $reason);
    }

    /**
     * @throws OrderDataProvidingException
     * @throws OrderDataInvalidException
     * @throws InvalidTaskStateException
     */
    private function loadOrderData(NotificationTask $task): void
    {
        $orderData = $this->orderDataProvider->getOrderData($task->getOrderId(), $task->getOrderEvent());

        $task->attachOrderData($orderData);
    }

    /**
     * @throws NotificationSendingException
     */
    private function dispatchNotification(NotificationTask $task): void
    {
        $notificationHandler = $this->handlerFactory->getForChannel($task->getChannel());

        $notificationHandler->handle($task);
    }
}
