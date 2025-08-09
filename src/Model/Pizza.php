<?php

namespace App\Model;

class Pizza {
    private $toppings = ['Цыпленок', 'Моцарелла', 'Ананас'];
    private $basePrice = 300.0;
    private $toppingPrice = 30.0;
    
    public function addTopping(string $topping): void
    {
        $this->toppings[] = $topping;
    }

    public function removeTopping(string $topping): void
    {
        if (in_array($topping, $this->toppings)) {
           $this->toppings = array_values(array_filter($this->toppings, fn($item) => $item != $topping));
        }
    }
    
    public function getToppings(): array 
    {
        return $this->toppings;
    }

    public function calculatePrice(): float
    {
        return $this->basePrice + count($this->toppings) * $this->toppingPrice;
    }
}