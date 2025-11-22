<?php

namespace Blarkinov\Hw1500\Application\Factory;

use Blarkinov\Hw1500\Domain\Fabric\FoodFabricInterface;
use Blarkinov\Hw1500\Domain\ValueObject\Food\Burger\BurgerCustom;
use Blarkinov\Hw1500\Domain\ValueObject\Food\Hotdog\HotdogCustom;
use Blarkinov\Hw1500\Domain\ValueObject\Food\Sandwich\SandwichCustom;

class FoodCustomFabric implements FoodFabricInterface
{
    public function makeBurger(): BurgerCustom
    {
        return new BurgerCustom();
    }
    public function makeHotdog(): HotdogCustom
    {
        return new HotdogCustom();
    }
    public function makeSandwich(): SandwichCustom
    {
        return new SandwichCustom();
    }
}
