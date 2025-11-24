<?php
declare(strict_types=1);

namespace Dinargab\Homework15\Model\Product\Decorators;

class TomatoProductDecorator extends AbstractProductDecorator
{
    protected const INGREDIENT_NAME = 'tomato';


    public function getPrice(): int
    {
        return $this->product->getPrice() + 3;
    }

    public function getIngredientName(): string
    {
        return self::INGREDIENT_NAME;
    }
}