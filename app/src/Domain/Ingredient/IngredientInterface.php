<?php

namespace App\Domain\Ingredient;

interface IngredientInterface
{
    public function getName(): string;

    public function getCount(): int;

    public function add(int $count): void;

    public function remove(int $count): void;
}