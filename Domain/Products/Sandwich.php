<?php

declare(strict_types=1);

namespace Domain\Products;

class Sandwich implements Product
{
    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return 'Sandwich';
    }

    /**
     * @return float|null
     */
    public function getPrice(): ?float
    {
        return 300;
    }
}