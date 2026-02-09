<?php

namespace Restaurant\Domain\Builders;

use Restaurant\Domain\Entities\Product;
use Restaurant\Domain\Interfaces\ProductBuilderInterface;
use Restaurant\Domain\Interfaces\ProductInterface;
use Restaurant\Domain\Interfaces\PricingServiceInterface;
use Restaurant\Domain\Enums\IngredientType;

abstract class AbstractProductBuilder implements ProductBuilderInterface
{
    protected Product $product;
    protected ?PricingServiceInterface $pricingService = null;

    public function __construct()
    {
        $this->reset();
    }

    public function setPricingService(PricingServiceInterface $pricingService): ProductBuilderInterface
    {
        $this->pricingService = $pricingService;
        return $this;
    }

    public function addIngredient(IngredientType $ingredient): ProductBuilderInterface
    {
        $this->product->addIngredient($ingredient->value);

        if ($this->pricingService) {
            $ingredientPrice = $this->pricingService->getIngredientPrice($ingredient);
            $newProduct = new Product(
                $this->product->getName(),
                $this->product->getDescription(),
                $this->product->getPrice() + $ingredientPrice
            );

            foreach ($this->product->getIngredients() as $existingIngredient) {
                $newProduct->addIngredient($existingIngredient);
            }

            $this->product = $newProduct;
        }

        return $this;
    }

    public function getProduct(): ProductInterface
    {
        $result = $this->product;
        $this->reset();
        return $result;
    }

    abstract public function reset(): void;
}
