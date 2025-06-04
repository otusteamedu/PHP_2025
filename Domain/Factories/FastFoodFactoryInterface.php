<?php

declare(strict_types=1);

namespace Domain\Factories;

use Domain\Products\Product;

interface FastFoodFactoryInterface
{
    public static function  createProduct(string $name): Product;
}


