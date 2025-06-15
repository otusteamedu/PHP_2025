<?php

namespace App\Application\Ingredient;

class SalatIngredient implements IngredientInterface
{
    public function add(): string {
        return 'salat';
    }
}