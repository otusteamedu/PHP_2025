<?php

namespace Blarkinov\Hw1500\Domain\Repository;

use Blarkinov\Hw1500\Domain\ValueObject\Food\Food;

interface FoodRepositoryInterface
{
    public function save(Food $food);
    public function getAll(): array;
    public function pop(): ?Food;
    public function shift(): ?Food;
    public function getCost():int;
}
