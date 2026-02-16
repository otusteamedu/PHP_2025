<?php

namespace App\Application\Decorator\Ingredient;

use App\Application\Decorator\ProductDecorator;

class GrassDecorator extends ProductDecorator
{
    public const string GRASS = 'Grass';

    protected function additionalIngredients(): array
    {
        return [self::GRASS];
    }
}
