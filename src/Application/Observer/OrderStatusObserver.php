<?php

namespace Blarkinov\Hw1500\Application\Observer;

use Blarkinov\Hw1500\Domain\Observer\OrderStatusEvent;
use Blarkinov\Hw1500\Domain\Observer\OrderStatusObserverInterface;
use Blarkinov\Hw1500\Domain\Observer\SubscriberOrderStatusInterface;

class OrderStatusObserver implements OrderStatusObserverInterface
{
    private array $subscribers = [];

    public function subscribe(SubscriberOrderStatusInterface $subscriber): void
    {
        $this->subscribers[] = $subscriber;
    }

    public function notify(OrderStatusEvent $event): void
    {
        foreach ($this->subscribers as $subscriber) {
            $subscriber->update($event);
        }
    }
}
