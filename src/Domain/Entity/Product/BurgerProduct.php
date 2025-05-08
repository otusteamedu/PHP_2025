<?php

namespace Domain\Entity\Product;

use Domain\Factory\Product\ProductFactoryInterface;
use Domain\Decorator\Product\ProductDecoratorInterface;

class BurgerProduct implements ProductFactoryInterface, ProductDecoratorInterface  
{  

    public function __construct(  
        private readonly string $recipe,  
    )  
    {  
    }  
  
    public function makeProduct(string $title)
    {  
        return "{$title} Бургер. {$this->recipe}";    
    }  

}  