<?php

declare(strict_types=1);

namespace App\Domain\Order;

class Order
{
    /** @var OrderItem[] */
    private array $items = [];

    public function __construct(public ?OrderStatus $status = null)
    {
    }

    public function addItem(OrderItem $item): void
    {
        $this->items[] = $item;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function setStatus(OrderStatus $status): void
    {
        $this->status = $status;
    }
}
