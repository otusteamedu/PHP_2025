<?php

namespace Shop\Strategy;

use Shop\Recipes\HotDog;
use Shop\Recipes\RecipeBase;

class ProductGenerator
{
    protected array $strategies = [];

    public function __construct(array $strategies = [])
    {
        $this->strategies = $strategies ?: [
            new BurgerStrategy(),
            new PizzaStrategy(),
            new HotDogStrategy(),
        ];
    }

    public function getProduct(string $type): RecipeBase
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($type)) {
                return $strategy->getRecipes();
            }
        }
        throw new \Exception('Продукт не найден');
    }
}