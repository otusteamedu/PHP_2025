<?php

declare(strict_types=1);

namespace App\Domain\Product\Decorator;

/**
 * Декоратор - добавка сыра
 * Паттерн: Декоратор
 */
class CheeseDecorator extends ProductDecorator
{
    public function getDescription(): string
    {
        return $this->product->getDescription() . ' + сыр';
    }

    public function getPrice(): float
    {
        return $this->product->getPrice() + $this->getAdditionPrice();
    }

    public function getIngredients(): array
    {
        return array_merge($this->product->getIngredients(), [$this->getAdditionName()]);
    }

    protected function getAdditionName(): string
    {
        return 'сыр';
    }

    protected function getAdditionPrice(): float
    {
        return 30.00;
    }
}
