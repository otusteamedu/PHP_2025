<?php

namespace App\Model;

interface Product {
    public function getName(): string;
    public function getPrice(): float;
    public function getIngredients(): array;
}