<?php

namespace Blarkinov\Hw1500\Domain\ValueObject\Food\Burger;

use Blarkinov\Hw1500\Domain\ValueObject\Food\Food;

class Burger extends Food
{

    protected int $cost = 15;

    public function checkStandart(): bool
    {
        if (random_int(0, 100) > 15)
            return true;
        else {
            $this->failedCookingDisplay();
            return false;
        }
    }

    public function calculate()
    {
        return $this->cost + random_int(0,5);
    }
}
