<?php

namespace Restaurant\Application\Handlers;

use Restaurant\Domain\Entities\Order;
use Restaurant\Domain\Enums\OrderStatus;

class OrderCreationHandler extends AbstractOrderHandler
{
    public function handle(Order $order): bool
    {
        echo "Обработка заказа #{$order->getId()} на этапе создания...\n";
        $order->setStatus(OrderStatus::CREATED);

        return $this->handleNext($order);
    }
}
