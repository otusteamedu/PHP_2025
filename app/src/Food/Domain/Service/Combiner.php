<?php
declare(strict_types=1);


namespace App\Food\Domain\Service;

use App\Food\Domain\Aggregate\Food;

class Combiner implements FoodCombinerInterface
{
    public function __construct(protected FoodCombinerInterface $combiner)
    {
    }

    public function addIngredient(Food $food): Food
    {
        return $this->combiner->addIngredient($food);
    }
}