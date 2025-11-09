<?php

declare(strict_types=1);

namespace App\Strategies;

use App\Products\ProductInterface;

class Order
{
    private array $_products = [];
    private OrderStrategyInterface $strategy;

    public function __construct(OrderStrategyInterface $strategy)
    {
        $this->strategy = $strategy;
    }

    public array $products {
        get {
            return $this->_products;
        }
    }

    public function addProduct(ProductInterface $product): self
    {
        $this->_products[] = $product;

        return $this;
    }

    public function setStrategy(OrderStrategyInterface $strategy): self
    {
        $this->strategy = $strategy;

        return $this;
    }

    public function getProducts(): array
    {
        return $this->products;
    }

    public function calculateTotal(): int
    {
        $total = $this->strategy->calculateTotal($this->products);

        return $this->strategy->applyDiscount($total);
    }

    public function getDescription(): string
    {
        return $this->strategy->getOrderDescription($this->products);
    }

    public function process(): array
    {
        return [
            'description' => $this->getDescription(),
            'total' => $this->calculateTotal(),
            'products_count' => count($this->products)
        ];
    }
}
