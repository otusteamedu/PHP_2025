<?php
namespace App\Factory;

use App\Products\Sandwich;
use App\Products\ProductInterface;

class SandwichFactory implements ProductFactoryInterface
{
    public function createProduct(): ProductInterface
    {
        return new Sandwich();
    }
}
