<?php

namespace Domain\Entity\Product;

use Domain\Factory\Product\ProductFactoryInterface;
use Domain\Decorator\Product\ProductDecoratorInterface;

class HotdogProduct implements ProductFactoryInterface, ProductDecoratorInterface  
{  

    public function __construct(  
        private readonly string $title,  
    )  
    {  
    }  
  
    public function makeProduct(string $title)
    {  
        return "{$title} Хотдог. {$this->title}";    
    }  

}  