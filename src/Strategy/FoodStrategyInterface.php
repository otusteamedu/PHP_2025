<?php declare(strict_types=1);

namespace App\Strategy;

use App\Core\FoodProductInterface;

interface FoodStrategyInterface
{
    public function createProduct(): FoodProductInterface;
}
