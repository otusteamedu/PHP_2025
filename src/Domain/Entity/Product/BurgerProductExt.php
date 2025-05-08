<?php

namespace Domain\Entity\Product;

use Domain\Decorator\Product\ProductDecoratorInterface;

class BurgerProductExt implements ProductDecoratorInterface  
{  

    public function __construct(  
        private readonly BurgerProduct $BurgerProduct,  
    )  
    {  
    }  
  
    public function makeProduct(string $title)
    {  
        return $this->BurgerProduct->makeProduct($title);   
    }  

} 