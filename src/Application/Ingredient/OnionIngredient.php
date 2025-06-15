<?php

namespace App\Application\Ingredient;

class OnionIngredient implements IngredientInterface
{
    public function add(): string {
        return 'onion';
    }
}