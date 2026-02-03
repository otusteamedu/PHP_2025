<?php

namespace App\Infrastructure\Strategies;

use App\Domain\Interfaces\ProductPrototypeStrategyInterface;
use App\Domain\Entities\Burger;

class BurgerPrototypeStrategy implements ProductPrototypeStrategyInterface
{
    public function createPrototype(): Burger
    {
        return new Burger();
    }
}