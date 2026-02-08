<?php

declare(strict_types=1);

namespace Ak\Hw\Domain\Observer;

use Ak\Hw\Domain\Notification\NotificationObserverInterface;

interface OrderObservableInterface
{
    public function addObserver(NotificationObserverInterface $observer): void;
    public function removeObserver(NotificationObserverInterface $observer): void;
    public function notifyObservers(): void;
}
