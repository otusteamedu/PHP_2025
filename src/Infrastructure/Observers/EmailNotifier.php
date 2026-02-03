<?php

namespace App\Infrastructure\Observers;

use App\Domain\Interfaces\ProductObserverInterface;
use App\Domain\Entities\Product;
use App\Domain\Enums\ProductStatus;

class EmailNotifier implements ProductObserverInterface
{
    public function update(Product $product, ProductStatus $previousStatus): void
    {
        if ($product->getStatus() === ProductStatus::READY) {
            // Здесь была бы реальная логика отправки email
            echo "Email: Product {$product->getName()} is ready!\n";
        }
    }
}