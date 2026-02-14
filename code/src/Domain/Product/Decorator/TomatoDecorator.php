<?php

declare(strict_types=1);

namespace App\Domain\Product\Decorator;

/**
 * Декоратор - добавка помидора
 * Паттерн: Декоратор
 */
class TomatoDecorator extends ProductDecorator
{
    public function getDescription(): string
    {
        return $this->product->getDescription() . ' + помидор';
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
        return 'помидор';
    }

    protected function getAdditionPrice(): float
    {
        return 25.00;
    }
}
