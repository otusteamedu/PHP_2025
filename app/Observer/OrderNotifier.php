<?php

declare(strict_types=1);

namespace App\Observer;

use Domain\Models\Order;

class OrderNotifier
{
    private array $observers = [];

    /**
     * @param OrderObserverInterface $observer
     * @return void
     */
    public function attach(OrderObserverInterface $observer): void
    {
        $this->observers[] = $observer;
    }

    /**
     * @param Order $order
     * @return void
     */
    public function notify(Order $order): void
    {
        foreach ($this->observers as $observer) {
            $observer->update($order);
        }
    }
}
