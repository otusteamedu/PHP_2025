<?php
declare(strict_types=1);


namespace App\Food\Domain\Service;

use App\Food\Domain\Aggregate\Food;

class BaseFoodCombiner implements FoodCombinerInterface
{

    public function addIngredient(Food $food): Food
    {
        return $food;
    }
}