<?php

namespace App\Domain\Ingredient\Storage;

use App\Domain\Ingredient\IngredientInterface;

interface IngredientStorageInterface
{
    public function get(string $name): IngredientInterface;

    public function has(string $name, int $count): bool;

    public function take(string $name, int $count): void;

    public function add(IngredientInterface $ingredient): void;
}