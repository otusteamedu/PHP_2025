<?php

namespace App\Domain\Entity\Product;

use App\Domain\Decorator\Product\ProductDecoratorInterface;

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