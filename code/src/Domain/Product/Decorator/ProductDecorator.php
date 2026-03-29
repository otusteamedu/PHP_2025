<?php

declare(strict_types=1);

namespace App\Domain\Product\Decorator;

use App\Domain\Interfaces\ProductInterface;

abstract class ProductDecorator implements ProductInterface
{
    public function __construct(
        protected ProductInterface $product
    ) {}

    public function getType(): string
    {
        return $this->product->getType();
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

    public function getIngredients(): array
    {
        return $this->product->getIngredients();
    }

    public function getAdditions(): array
    {
        $additions = [$this->getAdditionKey()];

        if ($this->product instanceof ProductDecorator) {
            $additions = array_merge($this->product->getAdditions(), $additions);
        }

        return $additions;
    }

    public function toArray(): array
    {
        return [
            'type' => $this->getType(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'price' => $this->getPrice(),
            'ingredients' => $this->getIngredients(),
        ];
    }

    abstract protected function getAdditionKey(): string;

    abstract protected function getAdditionName(): string;

    abstract protected function getAdditionPrice(): float;
}
