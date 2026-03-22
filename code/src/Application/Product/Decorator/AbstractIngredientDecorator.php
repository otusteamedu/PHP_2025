<?php

declare(strict_types=1);

namespace Otus\Code\Application\Product\Decorator;

use Otus\Code\Domain\Product\Contract\ProductInterface;

abstract class AbstractIngredientDecorator implements ProductInterface
{
    public function __construct(
        protected readonly ProductInterface $product,
    ) {
    }

    public function getName(): string
    {
        return $this->product->getName();
    }

    public function getIngredients(): array
    {
        return [...$this->product->getIngredients(), $this->getIngredientName()];
    }

    public function getBasePrice(): int
    {
        return $this->product->getBasePrice();
    }

    public function getPrice(): int
    {
        return $this->product->getPrice() + $this->getIngredientPrice();
    }

    public function describe(): string
    {
        return $this->getName() . ' (' . implode(', ', $this->getIngredients()) . ') - ' . $this->getPrice();
    }

    abstract protected function getIngredientName(): string;

    abstract protected function getIngredientPrice(): int;
}
