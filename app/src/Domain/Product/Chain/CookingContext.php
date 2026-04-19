<?php

namespace App\Domain\Product\Chain;

use App\Domain\Ingredient\Storage\IngredientStorageInterface;
use App\Domain\Product\ProductInterface;

class CookingContext
{
    public function __construct(
        public ProductInterface $product,
        public array $baseIngredients,
        public array $additionalIngredients,
        public IngredientStorageInterface $storage,
    ) {}
}