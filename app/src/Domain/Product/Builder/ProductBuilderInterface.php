<?php

namespace App\Domain\Product\Builder;

use App\Domain\Product\ProductInterface;

interface ProductBuilderInterface
{
    public function build(
        ProductInterface $product,
        array $baseIngredients,
        array $additionalIngredients
    ): ProductInterface;
}