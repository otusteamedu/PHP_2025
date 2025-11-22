<?php

namespace Blarkinov\Hw1500\Application\Factory;

use Blarkinov\Hw1500\Domain\Fabric\FoodFabricInterface;
use Blarkinov\Hw1500\Domain\ValueObject\Food\Burger\Burger;
use Blarkinov\Hw1500\Domain\ValueObject\Food\Hotdog\Hotdog;
use Blarkinov\Hw1500\Domain\ValueObject\Food\Sandwich\Sandwich;

class FoodFabric implements FoodFabricInterface
{
    public function makeBurger(): Burger {
        return new Burger;
    }
    public function makeHotdog(): Hotdog {
        return new Hotdog;
    }
    public function makeSandwich(): Sandwich {
        return new Sandwich;
    }
}
