<?php

namespace App\Domain\Product\Builder;

use App\Domain\Product\ProductInterface;

class ProductBuilder implements ProductBuilderInterface
{
    public function build(ProductInterface $product, array $baseIngredients, array $additionalIngredients): ProductInterface
    {
        //todo реализовать процесс готовки
        return $product;
    }
}