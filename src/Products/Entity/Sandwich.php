<?php declare(strict_types=1);

namespace Fastfood\Products\Entity;

class Sandwich extends BaseProduct
{
    protected string $description = "Сандвич (хлеб с колбасой)";
    protected float $cost = 120.0;

    public function setBase(string $description, float $cost): void
    {
        $this->description = $description;
        $this->cost = $cost;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getCost(): float
    {
        return $this->cost;
    }

    public function getBasicIngredients(): array
    {
        return ['salad', 'onion', 'tomato', 'mayo'];
    }
}