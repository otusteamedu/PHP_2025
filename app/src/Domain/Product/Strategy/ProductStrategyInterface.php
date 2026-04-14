<?php

namespace App\Domain\Product\Strategy;

use App\Domain\Product\ProductInterface;

interface ProductStrategyInterface
{
    public function create(): ProductInterface;

    public function getBaseIngredients(string $type): array;
}