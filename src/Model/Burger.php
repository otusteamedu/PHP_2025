<?php

namespace App\Model;

class Burger implements Product 
{
    private $name = 'Бургер';
    private $price = 200.0;
    private $ingredients = ['Булочка', 'Котлета'];
    
    public function getName(): string 
    { 
        return $this->name; 
    }

    public function getPrice(): float 
    { 
        return $this->price; 
    }

    public function getIngredients(): array 
    { 
        return $this->ingredients; 
    }
}