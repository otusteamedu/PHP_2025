<?php

namespace App\Application\Observer;

use App\Application\Notification\NotifierInterface;

class NotificationCookingObserver implements CookingObserverInterface
{
    public function __construct(private readonly NotifierInterface $notifier)
    {
    }

    public function update(CookingStatusChangedEvent $event): void
    {
        $this->notifier->notify(
            "$event->productName: $event->oldStatus -> $event->newStatus"
        );
    }
}
