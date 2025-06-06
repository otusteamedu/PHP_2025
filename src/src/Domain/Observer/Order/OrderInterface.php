<?php

namespace App\Domain\Observer\Order;

use App\Domain\Entity\Order\Order;

interface OrderInterface
{  
    public function notify(Order $order): void;  
}