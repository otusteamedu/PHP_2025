<?php

namespace App\Strategies;

use App\Products\ProductInterface;
use App\Products\Pizza;
use App\Adapters\PizzaAdapter;

class PizzaStrategy implements ProductStrategyInterface
{
    public function createBaseProduct(): ProductInterface
    {
        $pizza = new Pizza('Margherita');
        return new PizzaAdapter($pizza);
    }
}
