<?php

namespace App\Decorator;

use App\Model\Product;

class OnionDecorator extends ProductDecorator 
{
    public function __construct(Product $product) 
    {
        parent::__construct($product);
        $this->ingredientLabel = 'Лук';
        $this->priceDelta = 10.0;
        $this->nameSuffix = 'с луком';
    }
}