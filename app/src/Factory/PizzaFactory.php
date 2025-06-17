<?php
namespace App\Factory;

use App\Adapter\PizzaAdapter;
use App\Product\Food;
use App\Pizza\Pizza;

class PizzaFactory implements FoodFactory {
    public function createFood(): Food {
        return new PizzaAdapter(new Pizza());
    }
}
