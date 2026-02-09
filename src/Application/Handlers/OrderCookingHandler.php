<?php

namespace Restaurant\Application\Handlers;

use Restaurant\Domain\Entities\Order;
use Restaurant\Domain\Enums\OrderStatus;

class OrderCookingHandler extends AbstractOrderHandler
{
    public function handle(Order $order): bool
    {
        echo "Обработка заказа #{$order->getId()} на этапе приготовления...\n";
        $order->setStatus(OrderStatus::COOKING);

        return $this->handleNext($order);
    }
}
