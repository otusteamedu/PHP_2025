<?php

namespace App\Repository;

use App\Entity\Order;
use App\Mapper\OrderMapper;

class OrderRepository
{
    private OrderMapper $mapper;

    public function __construct(OrderMapper $mapper) {
        $this->mapper = $mapper;
    }

    public function findById(int $id): ?Order
    {
        return $this->mapper->fetchById($id);
    }

    /**
     * @return Order[]
     */
    public function findAllByUserId(int $userId): array
    {
        return $this->mapper->fetchAllByUserId($userId);
    }

    /**
     * @return Order[]
     */
    public function findAll(): array
    {
        return $this->mapper->fetchAll();
    }

    public function save(Order $order): void
    {
        $this->mapper->save($order);
    }
}
