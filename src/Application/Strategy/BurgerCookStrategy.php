<?php

namespace App\Application\Strategy;

use App\Application\Factory\FoodFactoryInterface;
use App\Domain\Entity\Interface\ProductInterface;

class BurgerCookStrategy implements CookStrategyInterface
{
    public function supports(ProductType $type): bool
    {
        return $type === ProductType::BURGER;
    }

    public function createBaseProduct(FoodFactoryInterface $factory): ProductInterface
    {
        return $factory->createBurger();
    }
}
