<?php

namespace Restaurant\Domain\Builders;

use Restaurant\Domain\Entities\Product;
use Restaurant\Domain\Enums\ProductType;

class SandwichBuilder extends AbstractProductBuilder
{
    public function reset(): void
    {
        $basePrice = $this->pricingService ? $this->pricingService->getProductBasePrice(ProductType::SANDWICH) : 0.0;
        $this->product = new Product('Сэндвич', 'Сэндвич с овощами', $basePrice);
    }
}
