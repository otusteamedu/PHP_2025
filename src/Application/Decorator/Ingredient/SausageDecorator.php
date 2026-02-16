<?php

namespace App\Application\Decorator\Ingredient;

use App\Application\Decorator\ProductDecorator;

class SausageDecorator extends ProductDecorator
{
    public const string SAUSAGE = 'Sausage';

    protected function additionalIngredients(): array
    {
        return [self::SAUSAGE];
    }
}
