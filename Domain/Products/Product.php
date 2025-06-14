<?php

declare(strict_types=1);

namespace Domain\Products;

interface Product
{
    /**
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @return float|null
     */
    public function getPrice(): ?float;
}
