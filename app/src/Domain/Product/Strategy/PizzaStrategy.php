<?php

namespace App\Domain\Product\Strategy;

use App\Domain\Product\Product;

class PizzaStrategy implements ProductStrategyInterface
{

    public function create(): Product
    {
        return new Product('pizza');
    }

    public function getBaseIngredients(string $type): array
    {
        return match ($type) {
           'classic' => [
               'pizza-crust' => 1,
               'ketchup' => 1,
            ],
            default => throw new \RuntimeException("Unknown pizza type")
        };
    }
}