<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifier;

use App\Domain\Order\OrderStatusChangedEvent;

class SmsNotifier
{
    public function __invoke(OrderStatusChangedEvent $event): void
    {
        echo "SMS: статус заказа изменён на {$event->newStatus->value}\n";
    }
}
