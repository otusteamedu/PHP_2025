<?php
declare(strict_types=1);

namespace Dinargab\Homework15\Model\Product\Decorators;

class BaconProductDecorator extends AbstractProductDecorator
{

    protected const INGREDIENT_NAME = "bacon";

    public function getIngredientName(): string
    {
        return self::INGREDIENT_NAME;
    }

    public function getPrice(): int
    {
        return $this->product->getPrice() + 3;
    }

}