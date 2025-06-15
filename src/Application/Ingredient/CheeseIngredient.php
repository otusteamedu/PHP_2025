<?php

namespace App\Application\Ingredient;

class CheeseIngredient implements IngredientInterface
{
    public function add(): string {
        return 'cheese';
    }
}