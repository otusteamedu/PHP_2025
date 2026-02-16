<?php

namespace App\Application\Decorator\Ingredient;

use App\Application\Decorator\ProductDecorator;

class MustardDecorator extends ProductDecorator
{
    public const string MUSTARD = 'Mustard';

    protected function additionalIngredients(): array
    {
        return [self::MUSTARD];
    }
}
