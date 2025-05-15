<?php

declare(strict_types=1);

namespace Domain\Decorator;

class OnionDecorator extends Decorator
{
    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->product->getName() . ', with onion';
    }

    /**
     * @return float|null
     */
    public function getPrice(): ?float
    {
        return $this->product->getPrice() + 20;
    }
}