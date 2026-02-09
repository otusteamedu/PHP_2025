<?php

namespace Restaurant\Domain\Interfaces;

interface ProductInterface
{
    public function getName(): string;

    public function getDescription(): string;

    public function getPrice(): float;

    public function addIngredient(string $ingredient): void;

    public function getIngredients(): array;
}
