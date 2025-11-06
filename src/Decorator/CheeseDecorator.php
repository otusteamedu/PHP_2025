<?php

declare(strict_types=1);

namespace Restaurant\Decorator;

class CheeseDecorator extends ProductDecorator
{
    public function getDescription(): string
    {
        return $this->wrappedProduct->getDescription() . ', сыр';
    }

    public function getPrice(): float
    {
        return $this->wrappedProduct->getPrice() + 30;
    }
}
