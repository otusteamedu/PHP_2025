<?php

namespace Domain\Entity\Product;

use Domain\Decorator\Product\ProductDecoratorInterface;

class HotdogProductExt implements ProductDecoratorInterface  
{  

    public function __construct(  
        private readonly HotdogProduct $HotdogProduct,  
    )  
    {  
    }  
  
    public function makeProduct(string $title)
    {  
        return $this->HotdogProduct->makeProduct($title);   
    }  

} 