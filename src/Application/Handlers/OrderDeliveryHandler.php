<?php

namespace Restaurant\Application\Handlers;

use Restaurant\Domain\Entities\Order;
use Restaurant\Domain\Enums\OrderStatus;

class OrderDeliveryHandler extends AbstractOrderHandler
{
    public function handle(Order $order): bool
    {
        echo "Обработка заказа #{$order->getId()} на этапе доставки...\n";
        $order->setStatus(OrderStatus::DELIVERED);

        return true;
    }
}
