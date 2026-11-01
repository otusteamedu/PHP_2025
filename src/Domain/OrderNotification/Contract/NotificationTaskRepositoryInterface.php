<?php

declare(strict_types=1);

namespace App\Domain\OrderNotification\Contract;

use App\Domain\OrderNotification\Entity\NotificationTask;
use App\Domain\OrderNotification\Exception\Task\NotificationTaskStorageException;
use App\Domain\OrderNotification\ValueObject\Task\TaskStatus;

interface NotificationTaskRepositoryInterface
{
    /**
     * @throws NotificationTaskStorageException
     */
    public function find(int $id): ?NotificationTask;

    /**
     * @throws NotificationTaskStorageException
     */
    public function persist(NotificationTask $task): void;

    /**
     * @throws NotificationTaskStorageException
     */
    public function transitionTo(NotificationTask $task, TaskStatus $status, ?string $reason = null): void;

    /**
     * @throws NotificationTaskStorageException
     */
    public function restart(NotificationTask $task): void;

    /**
     * @throws NotificationTaskStorageException
     */
    public function fail(NotificationTask $task, string $reason): void;
}
