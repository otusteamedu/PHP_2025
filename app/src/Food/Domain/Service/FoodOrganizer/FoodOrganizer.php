<?php

declare(strict_types=1);

namespace App\Food\Domain\Service\FoodOrganizer;

use App\Food\Domain\Aggregate\Food;
use App\Food\Domain\Aggregate\FoodInterface;
use App\Food\Domain\Aggregate\Ingredient\IngredientType;
use App\Food\Domain\Service\FoodFiller\BaseFoodFiller;
use App\Food\Domain\Service\FoodFiller\OnionFoodFiller;
use App\Food\Domain\Service\FoodFiller\PepperFoodFiller;
use App\Food\Domain\Service\FoodFiller\SaladFoodFiller;

class FoodOrganizer
{
    public function make(Food $food, IngredientType ...$ingredients): FoodInterface
    {
        $food = new BaseFoodFiller($food);
        foreach ($ingredients as $ingredient) {
            match ($ingredient) {
                IngredientType::ONION => $food = new OnionFoodFiller($food),
                IngredientType::PEPPER => $food = new PepperFoodFiller($food),
                IngredientType::SALAD => $food = new SaladFoodFiller($food),
            };
        }

        return $food;
    }
}
