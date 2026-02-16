<?php

namespace App\Application\Decorator\Ingredient;

use App\Application\Decorator\ProductDecorator;

class LettuceDecorator extends ProductDecorator
{
    public const string LETTUCE = 'Lettuce';

    protected function additionalIngredients(): array
    {
        return [self::LETTUCE];
    }
}
