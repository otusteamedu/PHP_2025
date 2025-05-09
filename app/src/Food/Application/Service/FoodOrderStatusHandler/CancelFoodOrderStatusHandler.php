<?php

declare(strict_types=1);

namespace App\Food\Application\Service\FoodOrderStatusHandler;

use App\Food\Domain\Aggregate\FoodCookingStatusType;
use App\Food\Domain\Aggregate\Order\FoodOrder;
use App\Food\Domain\Aggregate\Order\FoodOrderStatusType;

class CancelFoodOrderStatusHandler extends FoodOrderStatusHandler
{
    /**
     * @throws \Exception
     */
    public function handle(FoodOrder $order): void
    {
        if (FoodOrderStatusType::CANCELLED === $order->getStatus()) {
            foreach ($order->getFoodItems() as $foodItem) {
                if (FoodCookingStatusType::IN_QUEUE !== $foodItem->getCookingStatus()) {
                    throw new \Exception(sprintf('Order cannot be cancelled because of "%s" started cooking already', $foodItem->getTitle()));
                }
            }
        }
        parent::handle($order);
    }
}
