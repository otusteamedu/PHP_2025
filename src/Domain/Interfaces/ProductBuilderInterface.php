<?php

namespace Restaurant\Domain\Interfaces;

use Restaurant\Domain\Interfaces\PricingServiceInterface;
use Restaurant\Domain\Enums\IngredientType;

interface ProductBuilderInterface
{
    public function setPricingService(PricingServiceInterface $pricingService): ProductBuilderInterface;
    
    public function addIngredient(IngredientType $ingredient): ProductBuilderInterface;

    public function getProduct(): ProductInterface;
}
