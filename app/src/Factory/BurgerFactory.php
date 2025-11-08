<?php
namespace App\Factory;

use App\Product\Burger;
use App\Product\Food;

class BurgerFactory implements FoodFactory {
    public function createFood(): Food {
        return new Burger();
    }
}