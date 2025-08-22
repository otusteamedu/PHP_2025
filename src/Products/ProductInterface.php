<?php

declare(strict_types=1);

namespace App\Products;

interface ProductInterface
{
    public string $name {
        get;
    }

    public int $price {
        get;
    }

    public string $description {
        get;
    }
}
