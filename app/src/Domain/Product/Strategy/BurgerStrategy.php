<?php

namespace App\Domain\Product\Strategy;

use App\Domain\Product\Product;

class BurgerStrategy implements ProductStrategyInterface
{

    public function create(): Product
    {
        return new Product('burger');
    }

    public function getBaseIngredients(string $type): array
    {
        return match ($type) {
           'classic' => [
               'bun' => 1,
               'cutlet' => 1,
               'ketchup' => 1,
            ],
            default => throw new \RuntimeException("Unknown burger type")
        };
    }
}