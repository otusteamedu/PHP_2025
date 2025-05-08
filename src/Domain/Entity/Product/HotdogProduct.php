<?php

namespace Domain\Entity\Product;

use Domain\Factory\Product\ProductFactoryInterface;

class HotdogProduct implements ProductFactoryInterface  
{  

    public function __construct(  
        private readonly string $recipe,  
    )  
    {  
    }  
  
    public function makeProduct(string $title)
    {  
        return ($this->recipe) ? "{$title} Хотдог. {$this->recipe}" : "{$title} Хотдог. Стандартный рецепт";    
    }  

}  