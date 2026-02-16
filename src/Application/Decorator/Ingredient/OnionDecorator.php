<?php

namespace App\Application\Decorator\Ingredient;

use App\Application\Decorator\ProductDecorator;

class OnionDecorator extends ProductDecorator
{
    public const string ONION = 'Onion';

    protected function additionalIngredients(): array
    {
        return [self::ONION];
    }
}
