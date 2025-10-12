<?php

namespace App\Strategies;

use App\Products\BaseProduct;
use App\Products\Burger;

class BurgerStrategy implements ProductStrategyInterface
{
    public function createBaseProduct(): BaseProduct
    {
        return new Burger();
    }
}