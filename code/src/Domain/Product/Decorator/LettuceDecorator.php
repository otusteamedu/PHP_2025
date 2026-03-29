<?php

declare(strict_types=1);

namespace App\Domain\Product\Decorator;

class LettuceDecorator extends ProductDecorator
{
    public function getDescription(): string
    {
        return $this->product->getDescription() . ' + салат';
    }

    public function getPrice(): float
    {
        return $this->product->getPrice() + $this->getAdditionPrice();
    }

    public function getIngredients(): array
    {
        return array_merge($this->product->getIngredients(), [$this->getAdditionName()]);
    }

    protected function getAdditionKey(): string
    {
        return 'lettuce';
    }

    protected function getAdditionName(): string
    {
        return 'салат';
    }

    protected function getAdditionPrice(): float
    {
        return 20.00;
    }
}
