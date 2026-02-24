<?php

namespace Hafiz\Php2025\Builder;

use Hafiz\Php2025\Product\Product;

class Order {
    private array $products = [];

    public function addProduct(Product $product) {
        $this->products[] = $product;
    }

    public function getTotalCost(): float {
        return array_sum(array_map(fn($p) => $p->getCost(), $this->products));
    }

    public function describeOrder(): string {
        return implode("; ", array_map(fn($p) => $p->getDescription(), $this->products));
    }
}
