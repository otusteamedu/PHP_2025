<?php

declare(strict_types=1);

namespace Domain\Products;

class HotDog implements Product
{
    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return 'HotDog';
    }

    /**
     * @return float|null
     */
    public function getPrice(): ?float
    {
        return 250;
    }
}

