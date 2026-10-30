<?php

declare(strict_types=1);

namespace App\Domain\OrderNotification\Command;

use App\Domain\OrderNotification\ValueObject\Order\OrderEvent;
use App\Domain\OrderNotification\ValueObject\Order\OrderId;
use App\Domain\OrderNotification\ValueObject\Recipient\RecipientInterface;
use App\Domain\OrderNotification\ValueObject\Task\NotificationChannel;

readonly class CreateNotificationTaskCommand
{
    public function __construct(
        public OrderId $orderId,
        public OrderEvent $orderEvent,
        public NotificationChannel $channel,
        public RecipientInterface $recipient,
    ) {
    }
}
