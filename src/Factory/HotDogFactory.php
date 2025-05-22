<?php
namespace App\Factory;

use App\Products\HotDog;
use App\Products\ProductInterface;

class HotDogFactory implements ProductFactoryInterface
{
    public function createProduct(): ProductInterface
    {
        return new HotDog();
    }
}
