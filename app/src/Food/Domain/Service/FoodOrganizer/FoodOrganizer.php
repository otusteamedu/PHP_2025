<?php

declare(strict_types=1);

namespace App\Food\Domain\Service\FoodOrganizer;

use App\Food\Domain\Aggregate\Food;
use App\Food\Domain\Aggregate\Ingredient\IngredientType;
use App\Food\Domain\Service\BaseFoodCombiner;
use App\Food\Domain\Service\Combiner;
use App\Food\Domain\Service\OnionCombiner;
use App\Food\Domain\Service\PepperCombiner;
use App\Food\Domain\Service\SaladCombiner;

class FoodOrganizer
{
    public function make(Food $food, IngredientType ...$ingredients): Food
    {
        $combiner = new Combiner(new BaseFoodCombiner());
        foreach ($ingredients as $ingredient) {
            match ($ingredient) {
                IngredientType::ONION => $add = new OnionCombiner($combiner),
                IngredientType::PEPPER => $add = new PepperCombiner($combiner),
                IngredientType::SALAD => $add = new SaladCombiner($combiner),
            };
            $add->addIngredient($food);
        }

        return $food;
    }
}
