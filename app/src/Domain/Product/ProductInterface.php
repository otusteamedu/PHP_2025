<?php

namespace App\Domain\Product;
interface ProductInterface
{
    public function addIngredient(string $name, int $count): void;

    public function getIngredients(): array;
    public function getType(): string;
}