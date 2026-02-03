<?php

declare(strict_types=1);

namespace App\Domain\Order;

class OrderStatusChangedEvent
{
    public function __construct(
        public Order $order,
        public OrderStatus $newStatus,
    ) {
    }
}
