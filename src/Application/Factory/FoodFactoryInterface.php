<?php

namespace App\Application\Factory;

use App\Domain\Entity\Interface\BurgerInterface;
use App\Domain\Entity\Interface\HotdogInterface;
use App\Domain\Entity\Interface\SandwichInterface;

interface FoodFactoryInterface
{
    public function createBurger(): BurgerInterface;

    public function createHotDog(): HotdogInterface;

    public function createSandwich(): SandwichInterface;
}
