<?php

namespace App\Strategies;

use App\Products\BaseProduct;
use App\Products\Sandwich;

class SandwichStrategy implements ProductStrategyInterface
{
    public function createBaseProduct(): BaseProduct
    {
        return new Sandwich();
    }
}