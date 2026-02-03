<?php

namespace App\Infrastructure\Strategies;

use App\Domain\Interfaces\ProductPrototypeStrategyInterface;
use App\Domain\Entities\Sandwich;

class SandwichPrototypeStrategy implements ProductPrototypeStrategyInterface
{
    public function createPrototype(): Sandwich
    {
        return new Sandwich();
    }
}
