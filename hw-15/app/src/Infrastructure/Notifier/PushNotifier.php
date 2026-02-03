<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifier;

use App\Domain\Order\OrderStatusChangedEvent;

class PushNotifier
{
    public function __invoke(OrderStatusChangedEvent $event): void
    {
        echo "Push: статус заказа изменён на {$event->newStatus->value}\n";
    }
}
