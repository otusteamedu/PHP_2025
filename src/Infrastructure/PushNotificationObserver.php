<?php

namespace Restaurant\Infrastructure;

use Restaurant\Domain\Interfaces\ObserverInterface;
use Restaurant\Application\DTO\OrderEventDTO;

readonly class PushNotificationObserver implements ObserverInterface
{
    public function update($data): void
    {
        if ($data instanceof OrderEventDTO) {
            echo "Отправлено push-уведомление: Заказ #{$data->orderId} изменил статус на '{$data->status}' в {$data->timestamp}\n";
        }
    }
}
