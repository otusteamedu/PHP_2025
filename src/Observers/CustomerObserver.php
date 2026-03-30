<?php

namespace App\Observers;

use App\Products\BaseProduct;

class CustomerObserver implements CookingObserverInterface
{
    public function update(BaseProduct $product, string $oldStatus, string $newStatus): void
    {
        echo "Customer Notification: Your {$product->getName()} is now {$newStatus}\n";
    }
}