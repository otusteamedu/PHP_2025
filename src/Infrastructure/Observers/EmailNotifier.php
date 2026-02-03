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
            echo "Email: Продукт '{$product->getName()}' готов к выдаче!\n";
        }
    }
}