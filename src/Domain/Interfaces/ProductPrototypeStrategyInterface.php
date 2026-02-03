<?php

namespace App\Domain\Interfaces;

use App\Domain\Entities\Product;

interface ProductPrototypeStrategyInterface
{
    public function createPrototype(): Product;
}
