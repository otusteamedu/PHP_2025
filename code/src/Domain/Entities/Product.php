<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Domain\Interfaces\ProductInterface;

class Product implements ProductInterface
{
    /**
     * @param string $name Название продукта
     * @param string $description Описание продукта
     * @param float $price Базовая цена
     * @param string[] $ingredients Базовые ингредиенты
     */
    public function __construct(
        protected string $name,
        protected string $description,
        protected float $price,
        protected array $ingredients = []
    ) {}

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

    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'price' => $this->getPrice(),
            'ingredients' => $this->getIngredients(),
        ];
    }
}
