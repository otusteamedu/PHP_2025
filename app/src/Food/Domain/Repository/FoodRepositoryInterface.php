<?php

declare(strict_types=1);

namespace App\Food\Domain\Repository;

use App\Food\Domain\Aggregate\FoodInterface;

interface FoodRepositoryInterface
{
    public function add(FoodInterface $food): void;

    public function findById(string $foodId): ?FoodInterface;

    public function getByOrderId(string $orderId): array;
}
