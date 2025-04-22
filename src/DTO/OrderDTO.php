<?php declare(strict_types=1);

namespace App\DTO;

use App\Entity\Order;
use App\Entity\User;

readonly class OrderDTO
{
    public function __construct(
        public int    $id,
        public float  $totalAmount,
        public string $createdAt,
        public ?UserDTO $user,
    )
    {
    }

    static public function createFromEntity(Order $order): self
    {
        $userDTO = $order->getUser() ? UserDTO::createFromEntity($order->getUser()) : null;

        return new self(
            $order->getId(),
            $order->getTotalAmount(),
            $order->getCreatedAt()->format('Y-m-d H:i:s'),
            $userDTO
        );
    }
}
