<?php

namespace App\Application\Factory;

use App\Domain\Entity\Interface\BurgerInterface;
use App\Domain\Entity\Interface\HotdogInterface;
use App\Domain\Entity\Interface\SandwichInterface;
use App\Domain\Entity\VegetarianBurger;
use App\Domain\Entity\VegetarianHotdog;
use App\Domain\Entity\VegetarianSandwich;

class VegetarianFoodFactory implements FoodFactoryInterface
{
    public function createBurger(): BurgerInterface
    {
        return new VegetarianBurger();
    }

    public function createHotDog(): HotdogInterface
    {
        return new VegetarianHotdog();
    }

    public function createSandwich(): SandwichInterface
    {
        return new VegetarianSandwich();
    }
}
