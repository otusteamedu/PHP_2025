<?php

namespace App\Application\Strategy;

use App\Application\Factory\FoodFactoryInterface;
use App\Domain\Entity\Interface\ProductInterface;

class SandwichCookStrategy implements CookStrategyInterface
{
    public function supports(ProductType $type): bool
    {
        return $type === ProductType::SANDWICH;
    }

    public function createBaseProduct(FoodFactoryInterface $factory): ProductInterface
    {
        return $factory->createSandwich();
    }
}
