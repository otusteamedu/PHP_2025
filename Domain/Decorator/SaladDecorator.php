<?php

declare(strict_types=1);

namespace Domain\Decorator;

class SaladDecorator extends Decorator
{
    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->product->getName() . ', with salad';
    }

    /**
     * @return float|null
     */
    public function getPrice(): ?float
    {
        return $this->product->getPrice() + 30;
    }
}