<?php

namespace Blarkinov\Hw1500\Domain\Fabric;

use Blarkinov\Hw1500\Domain\ValueObject\Food\Burger\Burger;
use Blarkinov\Hw1500\Domain\ValueObject\Food\Hotdog\Hotdog;
use Blarkinov\Hw1500\Domain\ValueObject\Food\Sandwich\Sandwich;

interface FoodFabricInterface
{
    public function makeBurger(): Burger;
    public function makeSandwich(): Sandwich;
    public function makeHotdog(): Hotdog;
}
