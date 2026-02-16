<?php

namespace App\Application\Decorator\Ingredient;

use App\Application\Decorator\ProductDecorator;

class PepperDecorator extends ProductDecorator
{
    public const string PEPPER = 'Pepper';

    protected function additionalIngredients(): array
    {
        return [self::PEPPER];
    }
}
