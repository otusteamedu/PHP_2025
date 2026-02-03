<?php

namespace App\Domain\Interfaces;

use App\Domain\Entities\Product;

interface ProductDecoratorInterface
{
    public function decorate(Product $product, array $ingredients): Product;
}