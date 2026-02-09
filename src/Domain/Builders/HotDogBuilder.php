<?php

namespace Restaurant\Domain\Builders;

use Restaurant\Domain\Entities\Product;
use Restaurant\Domain\Enums\ProductType;

class HotDogBuilder extends AbstractProductBuilder
{
    public function reset(): void
    {
        $basePrice = $this->pricingService ? $this->pricingService->getProductBasePrice(ProductType::HOTDOG) : 0.0;
        $this->product = new Product('Хот-дог', 'Хот-дог с сосиской', $basePrice);
    }
}
