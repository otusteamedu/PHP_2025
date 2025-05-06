<?php

declare(strict_types=1);

namespace App\Core\Observer;

use App\Models\Order;

interface OrderObserverInterface
{
    public function update(Order $order);
}