<?php

namespace Elisad5791\Phpapp;

use ArrayIterator;
use IteratorAggregate;

class ProductCollection implements IteratorAggregate
{
    private array $products;

    public function __construct(Product ...$products)
    {
        $this->products = $products;
    }

    public function add(Product $product): void
    {
        $this->products[] = $product;
    }

    public function getById(int $id): ?product
    {
        foreach ($this->products as $product) {
            if ($product->getId() === $id) {
                return $product;
            }
        }
        return null;
    }

    public function count(): int
    {
        return count($this->products);
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->products);
    }
}