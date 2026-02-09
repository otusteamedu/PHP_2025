<?php

namespace Restaurant\Application\Handlers;

use Restaurant\Domain\Entities\Order;
use Restaurant\Domain\Enums\OrderStatus;

class OrderReadyHandler extends AbstractOrderHandler
{
    public function handle(Order $order): bool
    {
        echo "Обработка заказа #{$order->getId()} на этапе готовности...\n";
        $order->setStatus(OrderStatus::READY);

        return $this->handleNext($order);
    }
}
