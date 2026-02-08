<?php

namespace Shop\Strategy;

use Shop\Recipes\HotDog;
use Shop\Recipes\RecipeBase;

class HotDogStrategy implements IStrategyRecipes
{

    public function getType(): string
    {
        return 'hot_dog';
    }

    public function getRecipes(): RecipeBase
    {
        return new HotDog();
    }

    public function supports(string $type): bool
    {
        return $type === $this->getType();
    }
}