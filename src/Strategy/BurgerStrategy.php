<?php declare(strict_types=1);

namespace App\Strategy;

use App\Core\FoodProductInterface;
use App\Products\Burger;

class BurgerStrategy implements FoodStrategyInterface
{
    public function createProduct(): FoodProductInterface
    {
        return new Burger();
    }
}
