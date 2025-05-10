<?php

declare(strict_types=1);

namespace App\Food\Domain\Repository;

use App\Food\Domain\Aggregate\Order\FoodOrder;

interface FoodOrderRepositoryInterface
{
    public function add(FoodOrder $order): void;

    public function findById(string $orderId): ?FoodOrder;
}
