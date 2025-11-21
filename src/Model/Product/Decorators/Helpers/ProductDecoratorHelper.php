<?php
declare(strict_types=1);

namespace Dinargab\Homework15\Model\Product\Decorators\Helpers;

class ProductDecoratorHelper
{
    public static function addIngredientToName(string $name, string $ingredientName): string
    {
        return str_contains($name, "with") ? $name . ", " . $ingredientName : $name . " with " . $ingredientName;
    }
}