<?php

declare(strict_types=1);

namespace App\Infrastructure\Order;

final class Order
{
    private array $products;

    public function __construct(array $products)
    {
        $this->setProducts($products);
    }

    public function save()
    {
        // save order in db
    }

    public function getProducts(): array
    {
        return $this->products;
    }

    private function setProducts(array $products): void
    {
        $this->products = $products;
    }

}
