<?php

namespace Restaurant\Application\UseCases;

use Restaurant\Domain\Entities\Order;

readonly class CreateOrderUseCase
{
    public function execute(array $orderData): Order
    {
        $order = new Order($orderData['id']);

        foreach ($orderData['items'] as $item) {
            $order->addItem($item['product'], $item['quantity']);
        }
        return $order;
    }
}
