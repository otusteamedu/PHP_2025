<?php

namespace App\Domain\Ingredient;

class PizzaCrust extends BaseIngredient
{
    public function getName(): string
    {
        return 'pizza-crust';
    }
}