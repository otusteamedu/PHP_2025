<?php declare(strict_types=1);

namespace App\Strategy;

use App\Core\FoodProductInterface;
use App\Products\HotDog;

class HotDogStrategy implements FoodStrategyInterface
{
    public function createProduct(): FoodProductInterface
    {
        return new HotDog();
    }
}
