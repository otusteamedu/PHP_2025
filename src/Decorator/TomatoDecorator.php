<?php

declare(strict_types=1);

namespace Restaurant\Decorator;

class TomatoDecorator extends ProductDecorator
{
    public function getDescription(): string
    {
        return $this->wrappedProduct->getDescription() . ', помидор';
    }

    public function getPrice(): float
    {
        return $this->wrappedProduct->getPrice() + 25;
    }
}
