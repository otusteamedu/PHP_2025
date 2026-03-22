<?php

declare(strict_types=1);

namespace Otus\Code\Domain\Product\Enum;

enum ProductPrice: int
{
    case Burger = 220;
    case Sandwich = 180;
    case HotDog = 170;
}
