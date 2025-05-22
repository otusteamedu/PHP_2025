<?php
namespace App\Factory;

use App\Products\Burger;
use App\Products\ProductInterface;

class BurgerFactory implements ProductFactoryInterface
{
    public function createProduct(): ProductInterface
    {
        return new Burger();
    }
}
