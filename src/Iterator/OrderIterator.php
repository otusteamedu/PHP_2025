<?php

declare(strict_types=1);

namespace Restaurant\Iterator;

class OrderIterator implements OrderIteratorInterface
{
    private array $statuses;
    private int $position = 0;

    public function __construct(private readonly Order $order)
    {
        $this->statuses = [
            OrderStatus::CREATED,
            OrderStatus::COOKING,
            OrderStatus::READY,
            OrderStatus::DELIVERED,
        ];
    }

    public function hasNext(): bool
    {
        return $this->position < count($this->statuses);
    }

    public function next(): OrderStatus
    {
        $status = $this->statuses[$this->position];
        $this->order->setStatus($status);
        $this->position++;
        return $status;
    }

    public function current(): OrderStatus
    {
        return $this->statuses[$this->position] ?? $this->statuses[count($this->statuses) - 1];
    }

    public function reset(): void
    {
        $this->position = 0;
        $this->order->setStatus(OrderStatus::CREATED);
    }
}
