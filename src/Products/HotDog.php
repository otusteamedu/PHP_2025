<?php

namespace App\Products;

class HotDog extends BaseProduct
{
    public function __construct()
    {
        $this->name = 'Hot Dog';
        $this->ingredients = ['bun', 'sausage'];
    }

    public function getDescription(): string
    {
        return "Hot Dog with: " . implode(', ', $this->ingredients);
    }
}