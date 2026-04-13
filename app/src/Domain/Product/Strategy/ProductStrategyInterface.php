<?php

namespace App\Domain\Product\Strategy;

use App\Domain\Product\Product;

interface ProductStrategyInterface
{
    public function create(): Product;

    public function getBaseIngredients(string $type): array;
}