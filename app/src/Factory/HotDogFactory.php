<?php
namespace App\Factory;

use App\Product\HotDog;
use App\Product\Food;

class HotDogFactory implements FoodFactory {
    public function createFood(): Food {
        return new HotDog();
    }
}