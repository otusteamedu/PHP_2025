<?php

namespace App\Infrastructure\Strategies;

use App\Domain\Interfaces\ProductPrototypeStrategyInterface;
use App\Domain\Entities\HotDog;

class HotDogPrototypeStrategy implements ProductPrototypeStrategyInterface
{
    public function createPrototype(): HotDog
    {
        return new HotDog();
    }
}