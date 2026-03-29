<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Application\DTO\OrderResponse;
use App\Domain\Interfaces\OrderRepositoryInterface;
use Throwable;

class GetOrderUseCase
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository
    ) {}

    public function execute(string $orderId): OrderResponse
    {
        try {
            $order = $this->orderRepository->findById($orderId);

            if ($order === null) {
                return OrderResponse::error('Заказ не найден');
            }

            return OrderResponse::success(
                order: $order,
                message: sprintf('Текущий статус: %s', $order->getStatus()->getDescription())
            );
        } catch (Throwable $e) {
            return OrderResponse::error('Ошибка при получении заказа: ' . $e->getMessage());
        }
    }
}
