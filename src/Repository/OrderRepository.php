<?php

namespace App\Repository;

use App\Entity\Order;
use App\Mapper\OrderMapper;

class OrderRepository
{
    public function __construct(private OrderMapper $mapper) {}

    /**
     * @param int $id
     * @return Order|null
     */
    public function find(int $id): ?Order
    {
        return $this->mapper->fetchById($id);
    }

    /**
     * @param int $userId
     * @return Order[]
     */
    public function findAllByUserId(int $userId): array
    {
        return $this->mapper->fetchAllByUserId($userId);
    }

    /**
     * @param int $userId
     * @return array
     */
    public function findAllWithUser(int $userId): array
    {
        return $this->mapper->fetchAllWithUser($userId);
    }

    /**
     * @return Order[]
     */
    public function findAll(): array
    {
        return $this->mapper->fetchAll();
    }

    /**
     * @param Order $order
     * @return void
     */
    public function save(Order $order): void
    {
        $this->mapper->save($order);
    }
}
