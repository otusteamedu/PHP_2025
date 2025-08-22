<?php

declare(strict_types=1);

namespace App\Factories;

use App\Products\Burger;
use App\Products\ProductInterface;

class BurgerFactory implements ProductFactoryInterface
{
    public function createProduct(): ProductInterface
    {
        return new Burger();
    }
}
