<?php
namespace App\Order;

interface OrderFactory {
    public function createOrder(array $items): Order;
}
