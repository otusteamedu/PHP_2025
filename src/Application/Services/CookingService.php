<?php

namespace Restaurant\Application\Services;

use Restaurant\Domain\Entities\Order;
use Restaurant\Domain\Interfaces\CookingEventFactoryInterface;

readonly class CookingService
{
    public function __construct(
        private readonly CookingEventFactoryInterface $eventFactory
    ) {
    }

    public function cookOrder(Order $order): bool
    {
        echo "Начинаем приготовление заказа #{$order->getId()}...\n";

        $preEvent = $this->eventFactory->createPreCookingEvent();
        if (!$preEvent->execute($order)) {
            echo "Пред-событие не прошло, приготовление заказа #{$order->getId()} отменено.\n";
            return false;
        }

        echo "Идет процесс приготовления заказа #{$order->getId()}...\n";

        $postEvent = $this->eventFactory->createPostCookingEvent();
        if (!$postEvent->execute($order)) {
            echo "Пост-событие не прошло, заказ #{$order->getId()} утилизирован.\n";
            return false;
        }

        echo "Заказ #{$order->getId()} успешно приготовлен!\n";
        return true;
    }
}
