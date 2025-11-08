<?php
namespace App\Factory;

use App\Product\Sandwich;
use App\Product\Food;

class SandwichFactory implements FoodFactory {
    public function createFood(): Food {
        return new Sandwich();
    }
}