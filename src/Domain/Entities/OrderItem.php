<?php

namespace Restaurant\Domain\Entities;

use Restaurant\Domain\Interfaces\ProductInterface;

class OrderItem
{
    public function __construct(
        private readonly ProductInterface $product,
        private readonly int $quantity = 1
    ) {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Количество должно быть положительным числом');
        }
    }

    public function getProduct(): ProductInterface
    {
        return $this->product;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getTotalPrice(): float
    {
        return $this->product->getPrice() * $this->quantity;
    }

    public function getName(): string
    {
        return $this->product->getName();
    }
}
