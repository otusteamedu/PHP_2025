<?php
namespace App\Products;

class Burger implements ProductInterface
{
    public function getName(): string
    {
        return "Бургер";
    }
    
    public function getPrice(): float
    {
        return 5.99;
    }
    
    public function getDescription(): string
    {
        return "Классический бургер с котлетой";
    }
}
