<?php

namespace App\Adapters;

use App\Products\Pizza;

class PizzaAdapter implements PizzaInterface
{
    private Pizza $pizza;

    public function __construct(Pizza $pizza)
    {
        $this->pizza = $pizza;
    }

    public function prepare(): string
    {
        return $this->pizza->bake();
    }

    public function getIngredients(): array
    {
        return $this->pizza->getToppings();
    }
}