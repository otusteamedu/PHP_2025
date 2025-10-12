<?php

namespace App\Strategies;

use App\Products\ProductInterface;

interface ProductStrategyInterface
{
    public function createBaseProduct(): ProductInterface;
}
