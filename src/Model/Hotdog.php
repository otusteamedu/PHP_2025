<?php

namespace App\Model;

class Hotdog implements Product 
{
    private $name = 'Хотдог';
    private $price = 220;
    private $ingredients = ['Булочка', 'Сосиска'];
    
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