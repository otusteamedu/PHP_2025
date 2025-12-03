<?php
declare(strict_types=1);

namespace Dinargab\Homework15\Model\Product\Decorators;

class SaladProductDecorator extends AbstractProductDecorator
{
    protected const INGREDIENT_NAME = 'salad';

    public function getPrice(): int
    {
        return $this->product->getPrice() + 1;
    }

    public function getIngredientName(): string
    {
        return self::INGREDIENT_NAME;
    }
}