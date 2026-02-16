<?php

namespace App\Application\Decorator\Ingredient;

use App\Application\Decorator\ProductDecorator;

class CheeseDecorator extends ProductDecorator
{
    public const string CHEESE = 'Cheese';

    protected function additionalIngredients(): array
    {
        return [self::CHEESE];
    }
}
