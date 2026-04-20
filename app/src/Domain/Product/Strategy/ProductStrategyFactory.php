<?php

namespace App\Domain\Product\Strategy;

class ProductStrategyFactory implements ProductStrategyFactoryInterface
{

    public function create(string $productType): ProductStrategyInterface
    {
       return match ($productType) {
           'burger' => new BurgerStrategy(),
           'hot-dog' => new HotDogStrategy(),
           'pizza' => new PizzaStrategyAdapter(),
           'sandwich' => new SandwichStrategy(),
       };
    }
}