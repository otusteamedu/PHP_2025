<?php

namespace Blarkinov\Hw1500\Domain\ValueObject\Food\Hotdog;

use Blarkinov\Hw1500\Domain\ValueObject\Food\Food;

class Hotdog extends Food
{
    protected int $cost = 10;

    public function checkStandart(): bool
    {
        if (random_int(0, 100) > 5)
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
