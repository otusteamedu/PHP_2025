<?php

declare(strict_types=1);

namespace App\Domain\Cooking;

use App\Domain\Product\ProductInterface;

interface ProductCookerInterface
{
    public function preCook(ProductInterface $product): void;
    public function cook(ProductInterface $product): void;
    public function postCook(ProductInterface $product): void;
}
