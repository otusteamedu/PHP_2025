<?php declare(strict_types=1);

namespace App\Strategy;

use App\Core\FoodProductInterface;
use App\Products\Sandwich;

class SandwichStrategy implements FoodStrategyInterface
{
    public function createProduct(): FoodProductInterface
    {
        return new Sandwich();
    }
}
