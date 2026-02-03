<?php

namespace App\Infrastructure\Decorators;

use App\Domain\Interfaces\ProductDecoratorInterface;
use App\Domain\Entities\Product;

class ProductDecorator implements ProductDecoratorInterface
{
    public function decorate(Product $product, array $ingredients): Product
    {
        foreach ($ingredients as $ingredient) {
            $product->addIngredient($ingredient);
        }
        
        return $product;
    }
}