<?php

namespace Restaurant\Domain\Entities;

use Restaurant\Domain\Interfaces\ProductInterface;

class Product implements ProductInterface
{
    private readonly string $name;
    private readonly string $description;
    private readonly float $price;
    private array $ingredients = [];

    public function __construct(string $name, string $description, float $price)
    {
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function addIngredient(string $ingredient): void
    {
        $this->ingredients[] = $ingredient;
    }

    public function getIngredients(): array
    {
        return $this->ingredients;
    }
}
