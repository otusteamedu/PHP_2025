<?php
namespace App\Decorator;

use App\Products\ProductInterface;
use App\Decorator\ProductWithAdditive;

class ProductDecorator
{
    public function addAdditive(ProductInterface $product, string $additive): ProductInterface
    {
        return new ProductWithAdditive($product, $additive);
    }
}
