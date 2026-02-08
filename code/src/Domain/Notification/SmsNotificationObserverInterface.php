<?php

declare(strict_types=1);

namespace Ak\Hw\Domain\Notification;

use Ak\Hw\Domain\Observer\Order;

class SmsNotificationObserverInterface implements NotificationObserverInterface
{
    public function update(Order $order): void
    {
        echo "SMS уведомление: Статус заказа #{$order->getId()} теперь {$order->getStatus()}\n";
    }
}
