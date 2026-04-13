<?php

namespace App\Domain\Product;

class Product implements ProductInterface
{
    protected array $ingredients = [];
    protected string $type;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function addIngredient(string $name, int $count): void
    {
        if (!isset($this->ingredients[$name])) {
            $this->ingredients[$name] = 0;
        }

        $this->ingredients[$name] += $count;
    }

    public function getIngredients(): array
    {
        return $this->ingredients;
    }

    public function getType(): string
    {
        return $this->type;
    }
}