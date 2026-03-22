<?php

declare(strict_types=1);

namespace Otus\Code\Domain\Product\Entity;

use Otus\Code\Domain\Product\Enum\Ingredient;
use Otus\Code\Domain\Product\Enum\ProductPrice;

final class Sandwich extends AbstractProduct
{
    public function __construct()
    {
        parent::__construct(
            'Sandwich',
            [Ingredient::Bread->label(), Ingredient::Cheese->label(), Ingredient::Sauce->label()],
            ProductPrice::Sandwich->value,
        );
    }
}
