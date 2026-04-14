<?php

namespace App\Domain\Product\Strategy;

class ProductStrategyFactory implements ProductStrategyFactoryInterface
{

    public function create(string $productType): ProductStrategyInterface
    {
       return match ($productType) {
           'burger' => new BurgerStrategy(),
           'HotDogStrategy' => new HotDogStrategy(),
           'PizzaStrategy' => new PizzaStrategy(),
           'sandwich' => new SandwichStrategy(),
       };
    }
}