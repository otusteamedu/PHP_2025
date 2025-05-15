<?php

declare(strict_types=1);

namespace Domain\Decorator;

class CucumbersDecorator extends Decorator
{
    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->product->getName() . ', with cucumbers';
    }

    /**
     * @return float|null
     */
    public function getPrice(): ?float
    {
        return $this->product->getPrice() + 30;
    }
}