<?php

namespace Shop\Strategy;

use Shop\Adapter\Recipe\PizzaAdapter;
use Shop\Recipes\RecipeBase;

class PizzaStrategy implements IStrategyRecipes
{

    public function getType(): string
    {
        return 'pizza';
    }

    public function getRecipes(): RecipeBase
    {
        return new PizzaAdapter('tomato');
    }

    public function supports(string $type): bool
    {
        return $type === $this->getType();
    }
}