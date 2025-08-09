<?php

namespace App\Decorator;

use App\Model\Product;

abstract class ProductDecorator implements Product 
{
    protected $product;
    
    public function __construct(Product $product) 
    {
        $this->product = $product;
    }
    
    public function getName(): string 
    {
        return $this->product->getName();
    }
    
    public function getPrice(): float 
    {
        return $this->product->getPrice();
    }
    
    public function getIngredients(): array 
    {
        return $this->product->getIngredients();
    }
}