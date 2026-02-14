<?php

declare(strict_types=1);

namespace App\Domain\Product\Decorator;

use App\Domain\Interfaces\ProductInterface;

/**
 * Базовый декоратор продукта
 * Паттерн: Декоратор
 */
abstract class ProductDecorator implements ProductInterface
{
    public function __construct(
        protected ProductInterface $product
    ) {}

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

    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'price' => $this->getPrice(),
            'ingredients' => $this->getIngredients(),
        ];
    }

    /**
     * Получить название добавки
     */
    abstract protected function getAdditionName(): string;

    /**
     * Получить цену добавки
     */
    abstract protected function getAdditionPrice(): float;
}
