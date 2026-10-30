<?php

declare(strict_types=1);

namespace App\Domain\OrderNotification\Contract;

use App\Domain\OrderNotification\Entity\NotificationTask;
use App\Domain\OrderNotification\Exception\Task\NotificationTaskStorageException;

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
}
