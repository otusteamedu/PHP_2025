<?php

namespace App\Decorators;

use App\Products\BaseProduct;

class OnionDecorator implements ProductDecoratorInterface
{
    public function decorate(BaseProduct $product): BaseProduct
    {
        $product->addIngredient('onion');
        return $product;
    }
}