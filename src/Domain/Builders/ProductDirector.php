<?php

namespace Restaurant\Domain\Builders;

use Restaurant\Domain\Enums\IngredientType;
use Restaurant\Domain\Enums\ProductType;
use Restaurant\Domain\Interfaces\ProductBuilderInterface;
use Restaurant\Domain\Interfaces\ProductInterface;
use Restaurant\Domain\Interfaces\PricingServiceInterface;
use Restaurant\Domain\Builders\ProductBuilderFactory;

class ProductDirector
{
    public function __construct(
        private ProductBuilderFactory $factory,
        private ?PricingServiceInterface $pricingService = null
    ) {
    }

    public function setPricingService(PricingServiceInterface $pricingService): self
    {
        $this->pricingService = $pricingService;
        return $this;
    }

    private function configureBuilder(ProductType $type): ProductBuilderInterface
    {
        $builder = $this->factory->createBuilder($type);
        if ($this->pricingService) {
            $builder->setPricingService($this->pricingService);
            $builder->reset();
        }
        return $builder;
    }

    public function createStandardBurger(): ProductInterface
    {
        return $this->configureBuilder(ProductType::BURGER)
            ->addIngredient(IngredientType::BUN)
            ->addIngredient(IngredientType::BEEF_PATTY)
            ->addIngredient(IngredientType::SALAD)
            ->addIngredient(IngredientType::MAYO)
            ->getProduct();
    }

    public function createStandardSandwich(): ProductInterface
    {
        return $this->configureBuilder(ProductType::SANDWICH)
            ->addIngredient(IngredientType::BREAD)
            ->addIngredient(IngredientType::SAUSAGE)
            ->addIngredient(IngredientType::CHEESE)
            ->addIngredient(IngredientType::CUCUMBER)
            ->getProduct();
    }

    public function createStandardHotdog(): ProductInterface
    {
        return $this->configureBuilder(ProductType::HOTDOG)
            ->addIngredient(IngredientType::BUN)
            ->addIngredient(IngredientType::SAUSAGE_HOT_DOG)
            ->addIngredient(IngredientType::KETCHUP)
            ->addIngredient(IngredientType::MUSTARD)
            ->getProduct();
    }
}
