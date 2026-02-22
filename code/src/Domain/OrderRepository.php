<?php

declare(strict_types=1);

namespace Ak\Hw\Domain;

interface OrderRepository
{
    public function setOrderIsPaid(string $orderNumber, float $sum): bool;
}
