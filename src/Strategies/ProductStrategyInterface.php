<?php

namespace App\Strategies;

use App\Products\BaseProduct;

interface ProductStrategyInterface
{
    public function createBaseProduct(): BaseProduct;
}