<?php

namespace Restaurant\Domain\Builders;

use InvalidArgumentException;
use Restaurant\Domain\Enums\ProductType;
use Restaurant\Domain\Interfaces\ProductBuilderInterface;
use Restaurant\Domain\Interfaces\PricingServiceInterface;

class ProductBuilderFactory
{
    public function __construct(
        private ?PricingServiceInterface $pricingService = null
    ) {
    }

    public function setPricingService(PricingServiceInterface $pricingService): self
    {
        $this->pricingService = $pricingService;
        return $this;
    }

    public function createBuilder(ProductType $type): ProductBuilderInterface
    {
        $builder = match ($type) {
            ProductType::BURGER => new BurgerBuilder(),
            ProductType::SANDWICH => new SandwichBuilder(),
            ProductType::HOTDOG => new HotDogBuilder(),
            default => throw new InvalidArgumentException("Неизвестный тип продукта: {$type->name}")
        };
        if ($this->pricingService) {
            $builder->setPricingService($this->pricingService);
        }
        return $builder;
    }
}
