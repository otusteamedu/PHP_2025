<?php

declare(strict_types=1);

namespace Otus\Code\Application\Product\Decorator;

use Otus\Code\Domain\Product\Enum\Ingredient;
use Otus\Code\Domain\Product\Enum\IngredientPrice;

final class CheeseDecorator extends AbstractIngredientDecorator
{
    protected function getIngredientName(): string
    {
        return Ingredient::Cheese->label();
    }

    protected function getIngredientPrice(): int
    {
        return IngredientPrice::Cheese->value;
    }
}
