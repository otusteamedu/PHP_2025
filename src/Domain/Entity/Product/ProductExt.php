<?php

namespace Domain\Entity\Product;

use Domain\Decorator\Product\ProductDecoratorInterface;

class ProductExt implements ProductDecoratorInterface  
{  

    public function __construct(  
        private $product,  
    )  
    {  
    }  
  
    public function makeProduct(string $title)
    {  
        return $this->product->makeProduct($title);   
    }  

} 