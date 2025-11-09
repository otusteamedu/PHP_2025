<?php

declare(strict_types=1);

namespace App\Decorators;

use App\Products\ProductInterface;

interface ProductDecoratorInterface extends ProductInterface
{
    public function getDecoratedProduct(): ProductInterface;
}
