<?php

declare(strict_types=1);

namespace App\Observer;

use Domain\Models\Order;

interface OrderObserverInterface
{
    public function update(Order $order);
}