<?php

namespace App\Adapter;

use App\Model\Product;
use App\Model\Pizza;

class PizzaAdapter implements Product 
{
    public function __construct(private Pizza $pizza) {}
    
    public function getName(): string 
    {
        return 'Пицца';
    }
    
    public function getPrice(): float 
    {
        return $this->pizza->calculatePrice();
    }
    
    public function getIngredients(): array 
    {
        return $this->pizza->getToppings();
    }
}