<?php

namespace App\Domain\Ingredient;

abstract class BaseIngredient implements IngredientInterface
{
    protected int $count;

    public function __construct(int $count)
    {
        $this->count = $count;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function add(int $count): void
    {
        if ($count <= 0) {
            throw new \InvalidArgumentException('Count must be positive');
        }

        $this->count += $count;
    }

    public function remove(int $count): void
    {
        if ($count <= 0) {
            throw new \InvalidArgumentException('Count must be positive');
        }

        if ($this->count < $count) {
            throw new \RuntimeException(
                sprintf('%s is out of stock', $this->getName())
            );
        }

        $this->count -= $count;
    }
}