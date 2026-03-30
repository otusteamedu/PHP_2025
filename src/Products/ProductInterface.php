<?php

namespace App\Products;

interface ProductInterface
{
    public function getName(): string;
    public function getIngredients(): array;
    public function getStatus(): string;
    public function setStatus(string $status): void;
    public function addIngredient(string $ingredient): void;
    public function getDescription(): string;
}
