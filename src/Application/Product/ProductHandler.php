<?php

namespace App\Application\Product;

use App\Domain\Entity\Product;

abstract class ProductHandler
{
    public function __construct(private ?ProductHandler $nextHandler = null) {
    }

    public function setNext(ProductHandler $nextHandler): ProductHandler {
        $this->nextHandler = $nextHandler;
        return $nextHandler;
    }

    public function handle(Product $product): void {
        $this->nextHandler?->handle($product);
    }
}