<?php

namespace Restaurant\Domain\Decorators;

use Restaurant\Domain\Interfaces\ProductInterface;
use Restaurant\Domain\Interfaces\PricingServiceInterface;
use Restaurant\Domain\Enums\IngredientType;

class IngredientDecorator extends BaseProductDecorator
{
    public function __construct(
        ProductInterface $product,
        private readonly IngredientType $ingredient,
        private readonly PricingServiceInterface $pricingService
    ) {
        parent::__construct($product);
    }

    public function getIngredients(): array
    {
        $ingredients = $this->product->getIngredients();
        $ingredients[] = $this->ingredient->value;
        return $ingredients;
    }

    public function getPrice(): float
    {
        return $this->product->getPrice() + $this->pricingService->getIngredientPrice($this->ingredient);
    }

    public function setProduct(ProductInterface $product): void
    {
        $this->product = $product;
    }
}
