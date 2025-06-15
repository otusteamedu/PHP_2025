<?php

namespace App\Infrastructure\Handler;

use App\Application\Product\ProductHandler;
use App\Domain\Entity\Product;
use InvalidArgumentException;

class ProductStatusUtilizedHandler extends ProductHandler
{
    public function handle(Product $product): void {
        if ($product->getStatus() === 'utilized') {
            throw new InvalidArgumentException("Этот продукт был утилизирован");
        }

        parent::handle($product);
    }
}