<?php declare(strict_types=1);

namespace App\Service;

use App\DTO\OrderDTO;
use App\DTO\UserDTO;
use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;

class OrderService
{
    private OrderRepository $orderRepository;
    private UserRepository $userRepository;

    public function __construct(OrderRepository $orderRepository, UserRepository $userRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->userRepository = $userRepository;
    }

    public function create(float $totalAmount, int $userId): OrderDTO
    {
        $order = Order::createNew($userId, $totalAmount);
        $this->orderRepository->save($order);
        $user = $this->userRepository->findById($userId);
        $userDTO = $user ? UserDTO::createFromEntity($user) : null;

        return OrderDTO::createFromEntity($order, $userDTO);
    }

    /**
     * @return OrderDTO[]
     */
    public function getOrders(): array
    {
        $orders = $this->orderRepository->findAll();

        return array_map(function (Order $order) {
            $user = $this->userRepository->findById($order->getUserId());
            $userDTO = $user ? UserDTO::createFromEntity($user) : null;

            return OrderDTO::createFromEntity($order, $userDTO);
        }, $orders);
    }

    /**
     * @return OrderDTO[]
     */
    public function getOrdersByUserId(int $userId): array
    {
        $orders = $this->orderRepository->findAllByUserId($userId);

        return array_map(
            fn(Order $order) => OrderDTO::createFromEntity($order),
            $orders
        );
    }
}
