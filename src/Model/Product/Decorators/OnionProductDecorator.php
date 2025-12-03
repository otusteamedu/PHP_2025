<?php
declare(strict_types=1);

namespace Dinargab\Homework15\Model\Product\Decorators;

class OnionProductDecorator extends AbstractProductDecorator
{
    protected const INGREDIENT_NAME = 'onion';


    public function getPrice(): int
    {
        return $this->product->getPrice() + 2;
    }

    public function getIngredientName(): string
    {
        return self::INGREDIENT_NAME;
    }

}