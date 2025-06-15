<?php

namespace App\Application\Ingredient;

class CutletIngredient implements IngredientInterface
{
    public function add(): string {
        return 'cutlet';
    }
}