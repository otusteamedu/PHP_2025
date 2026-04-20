<?php

namespace App\Domain\Product\Strategy;

use App\Domain\Product\Product;
use App\Infrastructure\Recipe\Dodo\DodoRecipeProvider;

class PizzaStrategyAdapter implements ProductStrategyInterface
{

    public function __construct()
    {
    }

    public function create(): Product
    {
        return new Product($this->getProductType());
    }

    public function getProductType(): string
    {
        return 'pizza';
    }

    public function getBaseIngredients(string $type): array
    {
        $pizzaProvider = new DodoRecipeProvider();
        return $pizzaProvider->getRecipe(
            productType: $this->getProductType(),
            recipeType: $type
        );
    }
}