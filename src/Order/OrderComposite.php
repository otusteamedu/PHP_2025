<?php

namespace Shop\Order;

use Shop\Adapter\Enum\OrderStatus;
use Shop\Observer\Publisher\Publisher;
use Shop\Observer\Publisher\SmsPublisher;

class OrderComposite
{
    private array $items = [];

    private OrderStatus $status = OrderStatus::CREATED;

    public function __construct(string $description, private Publisher $publisher)
    {
    }


    public function addItem(OrderComponent $item): self
    {
        $this->items[] = $item;
        return $this;
    }

    public function removeItem(OrderComponent $item): void
    {
        $this->items = array_filter(
            $this->items,
            fn($i) => $i !== $item
        );
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getPrice(): float
    {
        return array_sum(array_map(
            fn($item) => $item->getPrice(),
            $this->items
        ));
    }

    public function setStatus(OrderStatus $status): void
    {
        $this->status = $status;
        $this->publisher->notify('order.status.changed');
    }

    public function getDescription(): string
    {
        return 'Статус заказа '. $this->getStatus()->value;
    }

    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    public function getName(): string
    {
        return 'Статус заказа '. $this->getStatus()->value;
    }
}