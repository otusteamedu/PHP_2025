<?php

namespace App\Application\Ingredient;

class KetchupIngredient implements IngredientInterface
{
    public function add(): string {
        return 'ketchup';
    }
}