<?php

namespace App\Domain\Interfaces;

use App\Domain\Entities\Product;
use App\Domain\Enums\ProductStatus;

interface ProductObserverInterface
{
    public function update(Product $product, ProductStatus $previousStatus): void;
}