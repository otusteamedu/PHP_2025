<?php

namespace App\Application\Factory;

use App\Domain\Entity\ClassicBurger;
use App\Domain\Entity\ClassicHotdog;
use App\Domain\Entity\ClassicSandwich;
use App\Domain\Entity\Interface\BurgerInterface;
use App\Domain\Entity\Interface\HotdogInterface;
use App\Domain\Entity\Interface\SandwichInterface;

class ClassicFoodFactory implements FoodFactoryInterface
{
    public function createBurger(): BurgerInterface
    {
        return new ClassicBurger();
    }

    public function createHotDog(): HotdogInterface
    {
        return new ClassicHotdog();
    }

    public function createSandwich(): SandwichInterface
    {
        return new ClassicSandwich();
    }
}
