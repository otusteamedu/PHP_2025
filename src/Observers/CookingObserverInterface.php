<?php

namespace App\Observers;

use App\Products\BaseProduct;

interface CookingObserverInterface
{
    public function update(BaseProduct $product, string $oldStatus, string $newStatus): void;
}