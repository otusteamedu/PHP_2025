<?php
namespace App\Factory;

use App\Product\Food;

interface FoodFactory {
    public function createFood(): Food;
}