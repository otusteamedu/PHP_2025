<?php

namespace Shop\Strategy;

use Shop\Recipes\BurgerRecipe;
use Shop\Recipes\RecipeBase;

class BurgerStrategy implements IStrategyRecipes
{

    public function getType(): string
    {
        return 'burger';
    }

    public function getRecipes(): RecipeBase
    {
        return new BurgerRecipe();
    }

    public function supports(string $type): bool
    {
        return $type === $this->getType();
    }
}