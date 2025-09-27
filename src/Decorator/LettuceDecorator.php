<?php

namespace App\Decorator;

use App\Model\Product;

class LettuceDecorator extends ProductDecorator 
{
    public function __construct(Product $product) 
    {
        parent::__construct($product);
        $this->ingredientLabel = 'Салат';
        $this->priceDelta = 20.0;
        $this->nameSuffix = 'с салатом';
    }
}