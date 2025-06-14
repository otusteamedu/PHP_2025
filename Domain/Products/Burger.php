<?php

declare(strict_types=1);

namespace Domain\Products;

class Burger implements Product
{
    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return 'Burger';
    }

    /**
     * @return float|null
     */
    public function getPrice(): ?float
    {
        return 350;
    }
}
