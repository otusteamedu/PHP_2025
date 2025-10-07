<?php

namespace App\Products;

class Burger extends BaseProduct
{
    public function __construct()
    {
        $this->name = 'Burger';
        $this->ingredients = ['bun', 'patty'];
    }

    public function getDescription(): string
    {
        return "Burger with: " . implode(', ', $this->ingredients);
    }
}