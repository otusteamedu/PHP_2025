<?php

declare(strict_types=1);

namespace Ak\Hw\Domain\Notification;

use Ak\Hw\Domain\Order\Order;
use Ak\Hw\Domain\Common\Observer\ObservableInterface;

class PushNotificationObserver implements NotificationObserverInterface
{
    public function update(ObservableInterface $observable): void
    {
        if ($observable instanceof Order) {
            echo "Push-уведомление: Статус заказа #{$observable->getId()} теперь {$observable->getStatus()}\n";
        }
    }
}
