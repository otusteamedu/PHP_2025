<?php

namespace App\Domain\Order;

class Order implements OrderInterface
{
    /** @var OrderItemModelInterface[] */
    private array $items = [];

    public function addItem(OrderItemModelInterface $orderItemModel): void
    {
        $this->items[] = $orderItemModel;
    }

    /**
     * @return OrderItemModelInterface[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}