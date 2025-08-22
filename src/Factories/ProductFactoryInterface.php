<?php

declare(strict_types=1);

namespace App\Factories;

use App\Products\ProductInterface;

interface ProductFactoryInterface
{
    public function createProduct(): ProductInterface;
}
