<?php

namespace Restaurant\Domain\Builders;

use Restaurant\Domain\Entities\Product;
use Restaurant\Domain\Enums\ProductType;
use Restaurant\Domain\Interfaces\PricingServiceInterface;

class BurgerBuilder extends AbstractProductBuilder
{
    public function reset(): void
    {
        $basePrice = $this->pricingService ? $this->pricingService->getProductBasePrice(ProductType::BURGER) : 0.0;
        $this->product = new Product('Бургер', 'Бургер с котлетой', $basePrice);
    }
}
