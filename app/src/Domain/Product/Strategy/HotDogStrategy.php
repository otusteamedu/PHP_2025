<?php

namespace App\Domain\Product\Strategy;

use App\Domain\Product\Product;

class HotDogStrategy implements ProductStrategyInterface
{

    public function create(): Product
    {
        return new Product('sandwich');
    }

    public function getBaseIngredients(string $type): array
    {
        return match ($type) {
           'classic' => [
               'hot-dog-bun' => 1,
               'sausage' => 1,
            ],
            default => throw new \RuntimeException("Unknown hot-dog type")
        };
    }
}