<?php

namespace App\Model;

class Sandwich implements Product 
{
    private $name = 'Сэндвич';
    private $price = 230;
    private $ingredients = ['Хлеб', 'Колбаса', 'Сыр'];
    
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