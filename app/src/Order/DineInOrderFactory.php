<?php
namespace App\Order;

class DineInOrderFactory implements OrderFactory {
    public function createOrder(array $items): Order {
        return new DineInOrder($items);
    }
}
