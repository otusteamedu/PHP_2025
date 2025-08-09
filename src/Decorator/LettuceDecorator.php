<?php

namespace App\Decorator;

use App\Model\Product;

class LettuceDecorator extends ProductDecorator 
{
    public function __construct(Product $product) 
    {
        parent::__construct($product);
    }
    
    public function getName(): string 
    {
        return parent::getName() . ' с салатом';
    }
    
    public function getPrice(): float 
    {
        return parent::getPrice() + 20;
    }
    
    public function getIngredients(): array 
    {
        $ingredients = parent::getIngredients();
        $ingredients[] = 'Салат';
        return $ingredients;
    }
}