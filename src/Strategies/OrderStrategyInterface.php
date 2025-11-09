<?php

declare(strict_types=1);

namespace App\Strategies;

interface OrderStrategyInterface
{
    public function calculateTotal(array $products): int;
    public function applyDiscount(int $total): int;
    public function getOrderDescription(array $products): string;
}
