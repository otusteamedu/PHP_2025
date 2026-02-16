<?php

namespace App\Application\Decorator\Ingredient;

use App\Application\Decorator\ProductDecorator;

class SauceDecorator extends ProductDecorator
{
    public const string SAUCE = 'Sauce';

    protected function additionalIngredients(): array
    {
        return [self::SAUCE];
    }
}
