<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Application\DTO\OrderResponse;
use App\Domain\Enums\OrderStatus;
use App\Domain\Interfaces\OrderRepositoryInterface;
use Throwable;

class CancelOrderUseCase
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

            $currentStatus = $order->getStatus();

            if (!$currentStatus->canTransitionTo(OrderStatus::CANCELLED)) {
                return OrderResponse::error(
                    sprintf(
                        'Невозможно отменить заказ в статусе "%s". Отмена доступна только для статусов: создан, подготовка, готовится',
                        $currentStatus->getDescription()
                    )
                );
            }

            $order->setStatus(OrderStatus::CANCELLED);
            $this->orderRepository->updateStatus($order);

            return OrderResponse::success(
                order: $order,
                message: 'Заказ успешно отменён'
            );
        } catch (Throwable $e) {
            return OrderResponse::error('Ошибка при отмене заказа: ' . $e->getMessage());
        }
    }
}
