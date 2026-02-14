<?php

declare(strict_types=1);

namespace App\Application\DTO;

use App\Domain\Entities\Order;

class OrderResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly string $message,
        public readonly ?array $order = null,
        public readonly array $cookingLogs = [],
        public readonly array $notifications = []
    ) {}

    public static function success(Order $order, array $cookingLogs = [], array $notifications = []): self
    {
        return new self(
            success: true,
            message: 'Заказ успешно создан и приготовлен',
            order: $order->toArray(),
            cookingLogs: $cookingLogs,
            notifications: $notifications
        );
    }

    public static function error(string $message, ?Order $order = null, array $cookingLogs = [], array $notifications = []): self
    {
        return new self(
            success: false,
            message: $message,
            order: $order?->toArray(),
            cookingLogs: $cookingLogs,
            notifications: $notifications
        );
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'order' => $this->order,
            'cooking_logs' => $this->cookingLogs,
            'notifications' => $this->notifications,
        ];
    }
}
