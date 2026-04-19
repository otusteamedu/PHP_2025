<?php

namespace App\Domain\Product\Builder;

use App\Domain\Ingredient\Storage\IngredientStorageInterface;
use App\Domain\Product\Chain\CookingContext;
use App\Domain\Product\Chain\CookingHandlerInterface;
use App\Domain\Product\ProductInterface;

class ProductBuilder implements ProductBuilderInterface
{
    public function __construct(
        private CookingHandlerInterface $chain,
        private IngredientStorageInterface $storage
    ) {}
    public function build(ProductInterface $product, array $baseIngredients, array $additionalIngredients): ProductInterface
    {
        $context = new CookingContext(
            $product,
            $baseIngredients,
            $additionalIngredients,
            $this->storage
        );

        $this->chain->handle($context);

        return $product;
    }
}