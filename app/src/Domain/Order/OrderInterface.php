<?php

namespace App\Domain\Order;

interface OrderInterface
{
    public function addItem(OrderItemModelInterface $orderItemModel): void;

    /**
     * @return OrderItemModelInterface[]
     */
    public function getItems(): array;
}