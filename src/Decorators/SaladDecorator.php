<?php

namespace App\Decorators;

use App\Products\BaseProduct;

class SaladDecorator implements ProductDecoratorInterface
{
    public function decorate(BaseProduct $product): BaseProduct
    {
        $product->addIngredient('salad');
        return $product;
    }
}