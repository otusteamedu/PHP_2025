<?php

declare(strict_types=1);

namespace Otus\Code\Application\Product\Decorator;

use Otus\Code\Domain\Product\Enum\Ingredient;
use Otus\Code\Domain\Product\Enum\IngredientPrice;

final class PepperDecorator extends AbstractIngredientDecorator
{
    protected function getIngredientName(): string
    {
        return Ingredient::Pepper->label();
    }

    protected function getIngredientPrice(): int
    {
        return IngredientPrice::Pepper->value;
    }
}
