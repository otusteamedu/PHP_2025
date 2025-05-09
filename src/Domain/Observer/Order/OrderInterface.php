<?php

namespace Domain\Observer\Order;

use Domain\Entity\Order\Order;

interface OrderInterface
{  
    public function notify(Order $order): void;  
}