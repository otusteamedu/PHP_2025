<?php

namespace App\Strategies;

use App\Products\BaseProduct;
use App\Products\HotDog;

class HotDogStrategy implements ProductStrategyInterface
{
    public function createBaseProduct(): BaseProduct
    {
        return new HotDog();
    }
}