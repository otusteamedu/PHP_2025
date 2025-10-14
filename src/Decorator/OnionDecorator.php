<?php

declare(strict_types=1);

namespace Restaurant\Decorator;

class OnionDecorator extends ProductDecorator
{
    public function getDescription(): string
    {
        return $this->wrappedProduct->getDescription() . ', лук';
    }

    public function getPrice(): float
    {
        return $this->wrappedProduct->getPrice() + 15;
    }
}
