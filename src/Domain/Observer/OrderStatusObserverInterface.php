<?php

namespace Blarkinov\Hw1500\Domain\Observer;

interface OrderStatusObserverInterface
{
    public function subscribe(SubscriberOrderStatusInterface $subscriber): void;

    public function notify(OrderStatusEvent $event): void;
}
