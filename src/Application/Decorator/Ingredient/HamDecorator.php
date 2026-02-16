<?php

namespace App\Application\Decorator\Ingredient;

use App\Application\Decorator\ProductDecorator;

class HamDecorator extends ProductDecorator
{
    public const string HAM = 'Ham';

    protected function additionalIngredients(): array
    {
        return [self::HAM];
    }
}
