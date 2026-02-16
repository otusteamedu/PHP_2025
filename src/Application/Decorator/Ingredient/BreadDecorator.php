<?php

namespace App\Application\Decorator\Ingredient;

use App\Application\Decorator\ProductDecorator;

class BreadDecorator extends ProductDecorator
{
    public const string BREAD = 'Bread';

    protected function additionalIngredients(): array
    {
        return [self::BREAD];
    }
}
