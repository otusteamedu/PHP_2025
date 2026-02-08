<?php

namespace Shop\Strategy;

use Shop\Recipes\RecipeBase;

interface IStrategyRecipes
{
    public function getType(): string;

    public function getRecipes(): RecipeBase;
    public function supports(string $type): bool;

}