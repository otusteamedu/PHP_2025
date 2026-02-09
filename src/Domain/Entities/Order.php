<?php

namespace Restaurant\Domain\Entities;

use InvalidArgumentException;
use Restaurant\Domain\Enums\OrderStatus;
use Restaurant\Domain\Interfaces\SubjectInterface;
use Restaurant\Domain\Interfaces\ObserverInterface;
use Restaurant\Domain\Interfaces\EventPublisherInterface;
use Restaurant\Domain\Interfaces\ProductInterface;

class Order implements SubjectInterface
{
    private array $items = [];
    private OrderStatus $status = OrderStatus::CREATED;

    public function __construct(
        private readonly int $id,
        private readonly SubjectInterface & EventPublisherInterface $eventNotifier
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function addItem(ProductInterface $product, int $quantity = 1): void
    {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('Количество должно быть положительным числом');
        }
        $this->items[] = new OrderItem($product, $quantity);
    }

    public function getProducts(): array
    {
        $products = [];
        foreach ($this->items as $item) {
            for ($i = 0; $i < $item->getQuantity(); $i++) {
                $products[] = $item->getProduct();
            }
        }
        return $products;
    }

    public function setStatus(OrderStatus $status): void
    {
        $this->status = $status;
        $this->notify();
    }

    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    public function getTotalPrice(): float
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item->getTotalPrice();
        }
        return $total;
    }

    public function getTotalQuantity(): int
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item->getQuantity();
        }
        return $total;
    }

    public function attach(ObserverInterface $observer): void
    {
        $this->eventNotifier->attach($observer);
    }

    public function detach(ObserverInterface $observer): void
    {
        $this->eventNotifier->detach($observer);
    }

    public function notify(mixed $data = null): void
    {
        $this->eventNotifier->publishOrderEvent($this->id, $this->status->value, date('Y-m-d H:i:s'));
    }
}
