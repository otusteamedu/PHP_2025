<?php declare(strict_types=1);

namespace App\DTO;

use App\Entity\Order;
use JsonSerializable;

readonly class OrderDTO implements JsonSerializable
{
    public function __construct(
        public int    $id,
        public float  $totalAmount,
        public string $createdAt,
        public ?UserDTO $user,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $data = [
            'id' => $this->id,
            'totalAmount' => $this->totalAmount,
            'createdAt' => $this->createdAt,
        ];

        if ($this->user !== null) {
            $data['user'] = $this->user;
        }

        return $data;
    }

    static public function createFromEntity(Order $order, ?UserDTO $userDTO = null): self
    {
        return new self(
            $order->getId(),
            $order->getTotalAmount(),
            $order->getCreatedAt()->format('Y-m-d H:i:s'),
            $userDTO
        );
    }
}
