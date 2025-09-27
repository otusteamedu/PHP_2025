<?php

namespace App\Decorator;

use App\Model\Product;

class CheeseDecorator extends ProductDecorator
{
    public function __construct(Product $product) 
    {
        parent::__construct($product);
        $this->ingredientLabel = 'Сыр';
        $this->priceDelta = 20.0;
        $this->nameSuffix = 'с сыром';
    }
}
