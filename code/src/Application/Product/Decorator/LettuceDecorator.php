<?php

declare(strict_types=1);

namespace Otus\Code\Application\Product\Decorator;

use Otus\Code\Domain\Product\Enum\Ingredient;
use Otus\Code\Domain\Product\Enum\IngredientPrice;

final class LettuceDecorator extends AbstractIngredientDecorator
{
    protected function getIngredientName(): string
    {
        return Ingredient::Lettuce->label();
    }

    protected function getIngredientPrice(): int
    {
        return IngredientPrice::Lettuce->value;
    }
}
