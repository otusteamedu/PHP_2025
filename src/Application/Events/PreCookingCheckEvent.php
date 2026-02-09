<?php

namespace Restaurant\Application\Events;

use Restaurant\Domain\Interfaces\CookingEventInterface;
use Restaurant\Domain\Entities\Order;

readonly class PreCookingCheckEvent implements CookingEventInterface
{
    public function execute(Order $order): bool
    {
        echo "Проверка заказа #{$order->getId()} перед приготовлением...\n";

        if (empty($order->getProducts())) {
            echo "Заказ #{$order->getId()} не содержит продуктов, приготовление невозможно.\n";
            return false;
        }

        echo "Заказ #{$order->getId()} прошел проверку, можно начинать приготовление.\n";
        return true;
    }
}
