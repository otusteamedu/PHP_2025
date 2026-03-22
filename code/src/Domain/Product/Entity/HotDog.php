<?php

declare(strict_types=1);

namespace Otus\Code\Domain\Product\Entity;

use Otus\Code\Domain\Product\Enum\Ingredient;
use Otus\Code\Domain\Product\Enum\ProductPrice;

final class HotDog extends AbstractProduct
{
    public function __construct()
    {
        parent::__construct(
            'Hot Dog',
            [Ingredient::Bun->label(), Ingredient::Sausage->label(), Ingredient::Sauce->label()],
            ProductPrice::HotDog->value,
        );
    }
}
