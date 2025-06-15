<?php

namespace App\Application\Ingredient;

class BunIngredient implements IngredientInterface
{
    public function add(): string {
        return 'bun';
    }
}