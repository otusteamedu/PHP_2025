<?php

namespace App\Decorator;

use App\Model\Product;

class MushroomsDecorator extends ProductDecorator 
{
    public function __construct(Product $product) 
    {
        parent::__construct($product);
        $this->ingredientLabel = 'Грибы';
        $this->priceDelta = 20.0;
        $this->nameSuffix = 'с грибами';
    }
}