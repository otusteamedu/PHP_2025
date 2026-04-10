<?php

namespace App\Domain\Ingredient;

class Cheese extends BaseIngredient
{

    public function getName(): string
    {
        return 'cheese';
    }
}