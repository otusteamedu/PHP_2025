<?php

namespace Shop\Recipes;

use Shop\Ingredients\BaseProduct\Base;
use Shop\Ingredients\BaseProduct\HotDogBun;
use Shop\Ingredients\Filling\Tomato;
use Shop\Ingredients\Meat\BaseMeat;
use Shop\Ingredients\Meat\Sausage;
use Shop\Ingredients\Sauce\BaseSauce;
use Shop\Ingredients\Sauce\Mustard;

class HotDog extends RecipeBase
{

    protected function getBase(): Base
    {
        return new HotDogBun();
    }

    protected function getFillingCollection(): array
    {
        return [new Tomato(),];
    }

    protected function getMeat(): BaseMeat
    {
        return new Sausage();
    }

    protected function needDuplicateBase(): bool
    {
        return false;
    }

    public function getDescription(): string
    {
        return 'Рецепт хот-дога';
    }

    protected function getSauce(): ?BaseSauce
    {
        return new Mustard();
    }
}