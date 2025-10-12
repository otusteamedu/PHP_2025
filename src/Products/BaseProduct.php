<?php

namespace App\Products;

abstract class BaseProduct implements ProductInterface
{
    protected string $name;
    protected array $ingredients = [];
    protected string $status = 'ordered';

    public function getName(): string
    {
        return $this->name;
    }

    public function getIngredients(): array
    {
        return $this->ingredients;
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
        $this->ingredients[] = $ingredient;
    }

    abstract public function getDescription(): string;
}
