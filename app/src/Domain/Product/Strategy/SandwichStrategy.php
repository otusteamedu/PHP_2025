<?php

namespace App\Domain\Product\Strategy;

use App\Domain\Product\Product;

class SandwichStrategy implements ProductStrategyInterface
{

    public function create(): Product
    {
        return new Product('sandwich');
    }

    public function getBaseIngredients(string $type): array
    {
        return match ($type) {
           'classic' => [
               'toast' => 1,
            ],
            default => throw new \RuntimeException("Unknown sandwich type")
        };
    }
}