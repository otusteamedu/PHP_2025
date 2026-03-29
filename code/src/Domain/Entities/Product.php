<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Domain\Interfaces\ProductInterface;

class Product implements ProductInterface
{
    public function __construct(
        protected string $type,
        protected string $name,
        protected string $description,
        protected float $price,
        protected array $ingredients = []
    ) {}

    public function getType(): string
    {
        return $this->type;
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

    public function getIngredients(): array
    {
        return $this->ingredients;
    }

    public function getAdditions(): array
    {
        return [];
    }

    public function toArray(): array
    {
        return [
            'type' => $this->getType(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'price' => $this->getPrice(),
            'ingredients' => $this->getIngredients(),
        ];
    }
}
