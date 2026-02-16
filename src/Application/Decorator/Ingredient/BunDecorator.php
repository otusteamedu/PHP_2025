<?php

namespace App\Application\Decorator\Ingredient;

use App\Application\Decorator\ProductDecorator;

class BunDecorator extends ProductDecorator
{
    public const string BUN = 'Bun';

    protected function additionalIngredients(): array
    {
        return [self::BUN];
    }
}
