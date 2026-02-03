<?php

declare(strict_types=1);

namespace App\Domain\Order;

use App\Domain\Product\ProductInterface;

class OrderItem
{
    public function __construct(
        private ProductInterface $product,
        private int $quantity,
    ) {
    }

    public function getProduct(): ProductInterface
    {
        return $this->product;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getPrice(): float
    {
        return $this->product->getPrice() * $this->quantity;
    }
}
