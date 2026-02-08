<?php

namespace Shop\Adapter\Recipe;

use Shop\External\Entity\Pizza;
use Shop\External\Recipes\PizzaRecipe;
use Shop\Ingredients\BaseProduct\Base;
use Shop\Ingredients\BaseProduct\Dough;
use Shop\Ingredients\Filling\Cheese;
use Shop\Ingredients\Filling\Filling;
use Shop\Ingredients\Filling\Tomato;
use Shop\Ingredients\Meat\BaseMeat;
use Shop\Ingredients\Meat\NoMeat;
use Shop\Ingredients\Sauce\BaseSauce;
use Shop\Recipes\RecipeBase;

class PizzaAdapter extends RecipeBase
{
    private Pizza $pizza;
    public function __construct(private string $type)
    {
        $this->pizza = (new PizzaRecipe())->make($this->type);
    }

    protected function getBase(): Base
    {
        return new Dough();
    }

    protected function getFillingCollection(): array
    {
        $result = [];
        foreach ($this->pizza->getToppings() as $topping) {
            $result[] =  $this->adaptTopping(topping: $topping);
        }

        return $result;
    }

    private function adaptTopping(string $topping): Filling
    {
        return match ($topping) {
            'tomato' => new Tomato(),
            'cheese' => new Cheese(),
            default => throw new \InvalidArgumentException("Неизвестная начинка: $topping")
        };
    }

    protected function getMeat(): BaseMeat
    {
        return new NoMeat();
    }

    protected function needDuplicateBase(): bool
    {
        return false;
    }

    public function getDescription(): string
    {
        return $this->pizza->getName();
    }

    protected function getSauce(): ?BaseSauce
    {
        return null;
    }
}