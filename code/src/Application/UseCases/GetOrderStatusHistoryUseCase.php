<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Domain\Interfaces\OrderRepositoryInterface;
use Throwable;

class GetOrderStatusHistoryUseCase
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository
    ) {}

    public function execute(string $orderId): array
    {
        try {
            $order = $this->orderRepository->findById($orderId);

            if ($order === null) {
                return [
                    'success' => false,
                    'message' => 'Заказ не найден',
                ];
            }

            $history = $this->orderRepository->getStatusHistory($orderId);

            return [
                'success' => true,
                'message' => 'История статусов получена',
                'order_id' => $orderId,
                'history' => $history,
            ];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'message' => 'Ошибка при получении истории: ' . $e->getMessage(),
            ];
        }
    }
}
