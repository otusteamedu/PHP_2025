<?php

namespace App\Adapters;

use App\Products\ProductInterface;
use App\Products\Pizza;

class PizzaAdapter implements ProductInterface
{
    private Pizza $pizza;
    private string $status = 'ordered';

    public function __construct(Pizza $pizza)
    {
        $this->pizza = $pizza;
    }

    public function getName(): string
    {
        return $this->pizza->getType() . ' Pizza';
    }

    public function getIngredients(): array
    {
        // Адаптируем toppings к ingredients
        return $this->pizza->getToppings();
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function addIngredient(string $ingredient): void
    {
        // Адаптируем addIngredient к addTopping
        $this->pizza->addTopping($ingredient);
    }

    public function getDescription(): string
    {
        $bakeMessage = $this->pizza->bake();
        return $bakeMessage . " with toppings: " . implode(', ', $this->getIngredients());
    }
}
