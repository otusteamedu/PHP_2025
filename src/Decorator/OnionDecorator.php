<?php

namespace App\Decorator;

use App\Model\Product;

class OnionDecorator extends ProductDecorator 
{
    public function __construct(Product $product) 
    {
        parent::__construct($product);
    }
    
    public function getName(): string 
    {
        return parent::getName() . ' с луком';
    }
    
    public function getPrice(): float 
    {
        return parent::getPrice() + 10;
    }
    
    public function getIngredients(): array 
    {
        $ingredients = parent::getIngredients();
        $ingredients[] = 'Лук';
        return $ingredients;
    }
}