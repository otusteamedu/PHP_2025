<?php

namespace Blarkinov\Hw1500\Domain\Strategies;

use Blarkinov\Hw1500\Domain\ValueObject\Food\Food;

interface CookingStrategyInterface
{
    public function cooking(Food $food): Food;
}
