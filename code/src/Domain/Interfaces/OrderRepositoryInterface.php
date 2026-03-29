<?php

declare(strict_types=1);

namespace App\Domain\Interfaces;

use App\Domain\Entities\Order;

interface OrderRepositoryInterface
{
    public function save(Order $order): void;

    public function findById(string $id): ?Order;

    public function updateStatus(Order $order): void;

    public function addStatusHistory(string $orderId, string $status, string $description): void;

    public function getStatusHistory(string $orderId): array;

    public function findPendingOrders(): array;
}
