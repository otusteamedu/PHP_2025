<?php

namespace App\Decorator;

use App\Model\Product;

class PepperoniDecorator extends ProductDecorator 
{
    public function __construct(Product $product) 
    {
        parent::__construct($product);
        $this->ingredientLabel = 'Пепперони';
        $this->priceDelta = 20.0;
        $this->nameSuffix = 'с пепперони';
    }
}
