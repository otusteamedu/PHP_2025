<?php

declare(strict_types=1);

namespace Restaurant\Decorator;

use Restaurant\Product\ProductInterface;

abstract class ProductDecorator implements ProductInterface
{
    public function __construct(protected readonly ProductInterface $wrappedProduct)
    {
    }

    public function getDescription(): string
    {
        return $this->wrappedProduct->getDescription();
    }

    public function getPrice(): float
    {
        return $this->wrappedProduct->getPrice();
    }

    public function cook(): void
    {
        $this->wrappedProduct->cook();
    }

    public function isQualityAcceptable(): bool
    {
        return $this->wrappedProduct->isQualityAcceptable();
    }
}
