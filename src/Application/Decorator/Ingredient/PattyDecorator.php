<?php

namespace App\Application\Decorator\Ingredient;

use App\Application\Decorator\ProductDecorator;

class PattyDecorator extends ProductDecorator
{
    public const string PATTY = 'Patty';

    protected function additionalIngredients(): array
    {
        return [self::PATTY];
    }
}
