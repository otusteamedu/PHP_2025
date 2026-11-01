<?php

declare(strict_types=1);

namespace App\Domain\OrderNotification\Channel;

use App\Domain\OrderNotification\Entity\NotificationTask;
use App\Domain\OrderNotification\Exception\Delivery\NotificationSendingException;
use App\Domain\OrderNotification\ValueObject\Task\NotificationChannel;

interface NotificationHandlerInterface
{
    public function supports(NotificationChannel $channel): bool;

    /**
     * @throws NotificationSendingException
     */
    public function handle(NotificationTask $task): void;
}
