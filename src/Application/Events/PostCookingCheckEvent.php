<?php

namespace Restaurant\Application\Events;

use Restaurant\Domain\Interfaces\CookingEventInterface;
use Restaurant\Domain\Entities\Order;

readonly class PostCookingCheckEvent implements CookingEventInterface
{
    public function __construct(
        private readonly float $qualityThreshold = 100.0
    ) {
    }

    public function execute(Order $order): bool
    {
        echo "Проверка качества заказа #{$order->getId()} после приготовления...\n";

        $randomQuality = rand(0, 100);
        echo "Сгенерированное качество приготовления: {$randomQuality}%\n";

        if ($randomQuality < $this->qualityThreshold) {
            echo "Заказ #{$order->getId()} имеет низкое качество ({$randomQuality}% < {$this->qualityThreshold}%), утилизируем.\n";
            return false;
        }

        echo "Заказ #{$order->getId()} прошел проверку качества ({$randomQuality}% >= {$this->qualityThreshold}%).\n";
        return true;
    }
}
