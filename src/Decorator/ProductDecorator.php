<?php

namespace Hafiz\Php2025\Decorator;

use Hafiz\Php2025\Product\Product;

abstract class ProductDecorator implements Product {
    protected Product $product;

    public function __construct(Product $product) {
        $this->product = $product;
    }

    public function getDescription(): string {
        return $this->product->getDescription();
    }

    public function getCost(): float {
        return $this->product->getCost();
    }
}
