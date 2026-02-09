<?php

namespace Restaurant\Domain\Decorators;

use Restaurant\Domain\Interfaces\ProductInterface;
use Restaurant\Domain\Interfaces\ProductDecoratorInterface;

abstract class BaseProductDecorator implements ProductDecoratorInterface
{
    public function __construct(
        protected ProductInterface $product
    ) {
    }

    public function getName(): string
    {
        return $this->product->getName();
    }

    public function getDescription(): string
    {
        return $this->product->getDescription();
    }

    public function getPrice(): float
    {
        return $this->product->getPrice();
    }

    public function addIngredient(string $ingredient): void
    {
        $this->product->addIngredient($ingredient);
    }

    public function getIngredients(): array
    {
        return $this->product->getIngredients();
    }
}
