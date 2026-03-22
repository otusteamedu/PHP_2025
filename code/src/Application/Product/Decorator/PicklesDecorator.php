<?php

declare(strict_types=1);

namespace Otus\Code\Application\Product\Decorator;

use Otus\Code\Domain\Product\Enum\Ingredient;
use Otus\Code\Domain\Product\Enum\IngredientPrice;

final class PicklesDecorator extends AbstractIngredientDecorator
{
    protected function getIngredientName(): string
    {
        return Ingredient::Pickles->label();
    }

    protected function getIngredientPrice(): int
    {
        return IngredientPrice::Pickles->value;
    }
}
