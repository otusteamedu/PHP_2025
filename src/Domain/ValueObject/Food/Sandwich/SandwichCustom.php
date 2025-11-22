<?php

namespace Blarkinov\Hw1500\Domain\ValueObject\Food\Sandwich;


class SandwichCustom extends Sandwich
{
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
        return $this->cost + random_int(6, 12);
    }
}
