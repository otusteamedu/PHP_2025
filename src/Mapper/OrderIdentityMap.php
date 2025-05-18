<?php declare(strict_types=1);

namespace App\Mapper;

use App\Entity\Order;

class OrderIdentityMap
{
    private array $orders = [];

    public function has(int $id): bool
    {
        return isset($this->orders[$id]);
    }

    public function get(int $id): Order
    {
        return $this->orders[$id];
    }

    public function add(Order $order): void
    {
        if ($order->getId() === null) {
            throw new \InvalidArgumentException("Cannot add order without ID to Identity Map.");
        }

        $this->orders[$order->getId()] = $order;
    }
}
