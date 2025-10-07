<?php

namespace App\Decorators;

use App\Products\BaseProduct;

class PepperDecorator implements ProductDecoratorInterface
{
    public function decorate(BaseProduct $product): BaseProduct
    {
        $product->addIngredient('pepper');
        return $product;
    }
}