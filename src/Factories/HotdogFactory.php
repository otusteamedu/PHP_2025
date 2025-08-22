<?php

declare(strict_types=1);

namespace App\Factories;

use App\Products\Hotdog;
use App\Products\ProductInterface;

class HotdogFactory implements ProductFactoryInterface
{
    public function createProduct(): ProductInterface
    {
        return new Hotdog();
    }
}
