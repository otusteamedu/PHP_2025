<?php

namespace App\Proxy;

use App\DataMappers\OrderMapper;

class OrderProxy
{
    private int $id;
    private int $userId;
    private OrderMapper $order;
    private array|null $orders = null;

    public function __construct(?int $id, ?int $userId)
    {
        $this->id = $id;
        $this->userId = $userId;
        $this->order = OrderMapper::getInstance();
    }

    public function getOrdersByUser(): array|null
    {
        if ($this->orders === null) {
            $this->orders = $this->directLoadOrders();
        }

        return $this->orders;
    }

    private function directLoadOrders(): array
    {
        return $this->order->fetchByUser($this->getUserId());
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
}
