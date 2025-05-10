<?php

declare(strict_types=1);

namespace App\Food\Application\Service\FoodOrderStatusHandler;

use App\Food\Domain\Aggregate\FoodCookingStatusType;
use App\Food\Domain\Aggregate\Order\FoodOrder;
use App\Food\Domain\Aggregate\Order\FoodOrderStatusType;

class ReadyFoodOrderStatusHandler extends FoodOrderStatusHandler
{
    /**
     * @throws \Exception
     */
    public function handle(FoodOrder $order): void
    {
        if (FoodOrderStatusType::READY === $order->getStatus()) {
            foreach ($order->getFoodItems() as $foodItem) {
                if (FoodCookingStatusType::PACKED !== $foodItem->getCookingStatus()) {
                    throw new \Exception(sprintf('Food "%s" is not packed yet', $foodItem->getTitle()));
                }
            }
        }
        parent::handle($order);
    }
}
