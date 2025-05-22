<?php
namespace App\Factory;

use App\Products\ProductInterface;

interface ProductFactoryInterface
{
    public function createProduct(): ProductInterface;
}
