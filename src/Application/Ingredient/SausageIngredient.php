<?php

namespace App\Application\Ingredient;

class SausageIngredient implements IngredientInterface
{
    public function add(): string {
        return 'sausage';
    }
}