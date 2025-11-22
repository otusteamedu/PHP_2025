<?php

namespace Blarkinov\Hw1500\Application\Strategies;

use Blarkinov\Hw1500\Domain\Strategies\CookingStrategyInterface;
use Blarkinov\Hw1500\Domain\ValueObject\Food\Food;

class BaseCookingStrategy  implements CookingStrategyInterface{
    public function cooking(Food $food): Food
    {
        return $food;
    }
}