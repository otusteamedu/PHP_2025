<?php

namespace Shop\Recipes;

use Exception;
use Shop\Ingredients\BaseProduct\Base;
use Shop\Ingredients\BaseProduct\Bun;
use Shop\Ingredients\Component;
use Shop\Ingredients\Filling\Tomato;
use Shop\Ingredients\Meat\BaseMeat;
use Shop\Ingredients\Meat\Cutlet;
use Shop\Ingredients\Product;
use Shop\Ingredients\Sauce\BaseSauce;
use Shop\Ingredients\Sauce\Mustard;

class BurgerRecipe extends RecipeBase
{
    function getBase(): Base
    {
        return new Bun();
    }

    function getFillingCollection(): array
    {
        return [new Tomato()];
    }

    function getMeat(): BaseMeat
    {
        return new Cutlet();
    }

    function needDuplicateBase(): bool
    {
        return true;
    }

    function getDescription(): string
    {
        return 'Бургер';
    }

    public function afterCook(Product $product): void
    {
        parent::afterCook($product);

        if (!$this->hasCorrectBurgerStructure($product)) {
            throw new \Exception('Бургер собран неправильно!');
        }
    }

    protected function getSauce(): ?BaseSauce
    {
        return new Mustard();
    }

    private function hasCorrectBurgerStructure(Product $product): bool
    {
        $components = $product->getComponents();

        if (count($components) < 2) {
            return false;
        }

        $first = reset($components);
        if (!$first instanceof Bun) {
            return false;
        }

        if ($this->needDuplicateBase()) {
            $last = end($components);
            if (!$last instanceof Bun) {
                return false;
            }
        }

        return true;
    }
}