<?php

namespace App\Factories;

use App\Products\BaseProduct;

interface ProductFactoryInterface
{
    public function createProduct(string $type): BaseProduct;
}