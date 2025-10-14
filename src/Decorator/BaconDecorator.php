<?php

declare(strict_types=1);

namespace Restaurant\Decorator;

class BaconDecorator extends ProductDecorator
{
    public function getDescription(): string
    {
        return $this->wrappedProduct->getDescription() . ', бекон';
    }

    public function getPrice(): float
    {
        return $this->wrappedProduct->getPrice() + 40;
    }
}
