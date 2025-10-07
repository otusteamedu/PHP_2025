<?php

namespace App\Observers;

use App\Products\BaseProduct;

class KitchenObserver implements CookingObserverInterface
{
    public function update(BaseProduct $product, string $oldStatus, string $newStatus): void
    {
        echo "Kitchen: Product {$product->getName()} status changed from {$oldStatus} to {$newStatus}\n";
    }
}