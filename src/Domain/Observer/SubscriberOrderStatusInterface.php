<?php

namespace Blarkinov\Hw1500\Domain\Observer;

interface SubscriberOrderStatusInterface {
    public function update(OrderStatusEvent $event):void;
}
