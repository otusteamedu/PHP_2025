<?php

declare(strict_types=1);

namespace Otus\Code\Domain\Product\Entity;

use Otus\Code\Domain\Product\Enum\Ingredient;
use Otus\Code\Domain\Product\Enum\ProductPrice;

final class Burger extends AbstractProduct
{
    public function __construct()
    {
        parent::__construct(
            'Burger',
            [Ingredient::Bun->label(), Ingredient::Cutlet->label(), Ingredient::Sauce->label()],
            ProductPrice::Burger->value,
        );
    }
}
