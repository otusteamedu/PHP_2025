<?php

namespace App\Domain\Entity\Product;

use App\Domain\Factory\Product\ProductFactoryInterface;

class BurgerProduct implements ProductFactoryInterface  
{  

    public function __construct(  
        private readonly ?string $recipe=null,  
    )  
    {  
    }  
  
    public function makeProduct(string $title)
    {  
        return ($this->recipe) ? "{$title} Бургер. {$this->recipe}" : "{$title} Бургер. Стандартный рецепт";    
    }  

}  