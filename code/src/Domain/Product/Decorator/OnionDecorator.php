<?php

declare(strict_types=1);

namespace App\Domain\Product\Decorator;

class OnionDecorator extends ProductDecorator
{
    public function getDescription(): string
    {
        return $this->product->getDescription() . ' + лук';
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
        return 'onion';
    }

    protected function getAdditionName(): string
    {
        return 'лук';
    }

    protected function getAdditionPrice(): float
    {
        return 15.00;
    }
}
