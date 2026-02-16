<?php

namespace App\Application\Strategy;

use App\Application\Factory\FoodFactoryInterface;
use App\Domain\Entity\Interface\ProductInterface;

class HotdogCookStrategy implements CookStrategyInterface
{
    public function supports(ProductType $type): bool
    {
        return $type === ProductType::HOTDOG;
    }

    public function createBaseProduct(FoodFactoryInterface $factory): ProductInterface
    {
        return $factory->createHotDog();
    }
}
