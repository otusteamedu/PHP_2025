<?php

declare(strict_types=1);

namespace Otus\Code\Domain\Product\Enum;

enum IngredientPrice: int
{
    case Lettuce = 20;
    case Onion = 15;
    case Pepper = 10;
    case Cheese = 30;
    case Pickles = 25;
}
