<?php

namespace Blarkinov\Hw1500\Domain\ValueObject\Food;

use Blarkinov\Hw1500\Domain\Composite\OrderCostInterface;

abstract class Food implements OrderCostInterface
{

    public function __construct() {}

    public function searchIngredients()
    {
        echo basename(static::class) . " - searching ingredients...\n";
        sleep(1);
    }

    abstract public function checkStandart(): bool;

    protected function failedCookingDisplay(): void
    {
        echo basename(static::class) . " failed cooking! repeat cooking\n";
        sleep(1);
    }
}
