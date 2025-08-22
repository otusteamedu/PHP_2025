<?php

declare(strict_types=1);

namespace App\Factories;

use App\Products\Sandwich;
use App\Products\ProductInterface;

class SandwichFactory implements ProductFactoryInterface
{
    public function createProduct(): ProductInterface
    {
        return new Sandwich();
    }
}
