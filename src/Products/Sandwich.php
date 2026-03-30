<?php

namespace App\Products;

class Sandwich extends BaseProduct
{
    public function __construct()
    {
        $this->name = 'Sandwich';
        $this->ingredients = ['bread', 'cheese'];
    }

    public function getDescription(): string
    {
        return "Sandwich with: " . implode(', ', $this->ingredients);
    }
}