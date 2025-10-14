<?php

declare(strict_types=1);

namespace Restaurant\Decorator;

class PepperDecorator extends ProductDecorator
{
    public function getDescription(): string
    {
        return $this->wrappedProduct->getDescription() . ', перец';
    }

    public function getPrice(): float
    {
        return $this->wrappedProduct->getPrice() + 10;
    }
}
