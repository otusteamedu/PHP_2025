<?php

declare(strict_types=1);

namespace Restaurant\Iterator;

use Restaurant\Product\ProductInterface;

class Order
{
    private array $products = [];
    private OrderStatus $status;

    public function __construct(private readonly int $id)
    {
        $this->status = OrderStatus::CREATED;
    }

    public function addProduct(ProductInterface $product): void
    {
        $this->products[] = $product;
    }

    public function getProducts(): array
    {
        return $this->products;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    public function setStatus(OrderStatus $status): void
    {
        $this->status = $status;
    }

    public function getTotalPrice(): float
    {
        return array_reduce(
            $this->products,
            fn(float $sum, ProductInterface $product) => $sum + $product->getPrice(),
            0
        );
    }
}
