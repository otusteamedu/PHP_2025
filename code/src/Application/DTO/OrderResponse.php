<?php

declare(strict_types=1);

namespace App\Application\DTO;

use App\Domain\Entities\Order;

class OrderResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly string $message,
        public readonly ?array $order = null
    ) {}

    public static function success(
        Order $order,
        string $message = 'Заказ успешно создан'
    ): self {
        return new self(
            success: true,
            message: $message,
            order: $order->toArray()
        );
    }

    public static function error(string $message, ?Order $order = null): self
    {
        return new self(
            success: false,
            message: $message,
            order: $order?->toArray()
        );
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'order' => $this->order,
        ];
    }
}
