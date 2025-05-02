<?php
declare(strict_types=1);


namespace App\Food\Domain\Service;

use App\Food\Domain\Aggregate\Food;
use App\Food\Domain\Aggregate\Ingredient\OnionIngredient;

class OnionCombiner extends Combiner
{
    public function addIngredient(Food $food): Food
    {
        $food = parent::addIngredient($food);
        $food->addIngredient(new OnionIngredient());

        return $food;
    }
}