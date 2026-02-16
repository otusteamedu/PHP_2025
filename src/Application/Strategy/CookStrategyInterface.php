<?php

namespace App\Application\Strategy;

use App\Application\Factory\FoodFactoryInterface;
use App\Domain\Entity\Interface\ProductInterface;

interface CookStrategyInterface
{
    public function supports(ProductType $type): bool;

    public function createBaseProduct(FoodFactoryInterface $factory): ProductInterface;
}
