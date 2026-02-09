<?php

namespace Restaurant\Domain\Interfaces;

use Restaurant\Domain\Enums\IngredientType;
use Restaurant\Domain\Enums\ProductType;

interface PricingServiceInterface
{
    public function getIngredientPrice(IngredientType $ingredient): float;

    public function getProductBasePrice(ProductType $productName): float;
}
