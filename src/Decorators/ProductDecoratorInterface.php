<?php

namespace App\Decorators;

use App\Products\BaseProduct;

interface ProductDecoratorInterface
{
    public function decorate(BaseProduct $product): BaseProduct;
}