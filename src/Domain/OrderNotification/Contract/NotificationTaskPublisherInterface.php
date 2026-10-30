<?php

declare(strict_types=1);

namespace App\Domain\OrderNotification\Contract;

use App\Domain\OrderNotification\Entity\NotificationTask;
use App\Domain\OrderNotification\Exception\Task\NotificationTaskPublishingException;

interface NotificationTaskPublisherInterface
{
    /**
     * @throws NotificationTaskPublishingException
     */
    public function publish(NotificationTask $task): void;
}
