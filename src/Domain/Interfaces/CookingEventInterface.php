<?php

namespace Restaurant\Domain\Interfaces;

use Restaurant\Domain\Entities\Order;

interface CookingEventInterface
{
    public function execute(Order $order): bool;
}
