<?php
namespace App\Order;

class TakeawayOrderFactory implements OrderFactory {
    public function createOrder(array $items): Order {
        return new TakeawayOrder($items);
    }
}
