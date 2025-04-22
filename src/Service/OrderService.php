<?php declare(strict_types=1);

namespace App\Service;

use App\DTO\OrderDTO;
use App\Entity\Order;
use App\Repository\OrderRepository;

class OrderService
{
    public function __construct(private OrderRepository $orderRepository)
    {
    }

    public function create(float $totalAmount, int $userId): OrderDTO
    {
        $order = new Order();
        $order->setTotalAmount($totalAmount);
        $order->setUserId($userId);

        $this->orderRepository->save($order);
        $createdOrder = $this->orderRepository->find((int)$order->getId());
        return OrderDTO::createFromEntity($createdOrder);
    }

    public function getOrders(): array
    {
        $orders = $this->orderRepository->findAll();
        return array_map(
            fn(Order $order) => OrderDTO::createFromEntity($order),
            $orders
        );
    }

    public function getOrdersByUserId(int $userId): array
    {
        $orders = $this->orderRepository->findAllByUserId($userId);
        return array_map(
            fn(Order $order) => OrderDTO::createFromEntity($order),
            $orders
        );
    }

    public function getOrdersWithUser(int $userId): array
    {
        $orders = $this->orderRepository->findAllWithUser($userId);
        return array_map(
            fn(Order $order) => OrderDTO::createFromEntity($order),
            $orders
        );
    }
}
