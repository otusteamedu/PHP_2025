<?php

namespace Blarkinov\Hw1500\Application\Repository\Food;

use Blarkinov\Hw1500\Domain\Repository\FoodRepositoryInterface;
use Blarkinov\Hw1500\Domain\ValueObject\Food\Food;

class FoodRepository implements FoodRepositoryInterface
{
    protected array $foods = [];

    public function save(Food $food)
    {
        $this->foods[] = $food;
    }

    public function getAll(): array
    {
        return $this->foods;
    }

    public function pop(): ?Food
    {
        return array_pop($this->foods);
    }

    public function shift(): ?Food
    {
        return  array_shift($this->foods);
    }

    public function getCost(): int
    {
        $cost = 0;
        foreach ($this->foods as $food) {
            $cost+=$food->calculate();
        }
        return $cost;
    }
}
