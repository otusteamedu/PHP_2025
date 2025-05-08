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
        if ($order->getStatus() === FoodOrderStatusType::READY) {
            foreach ($order->getFoodItems() as $foodItem) {
                if ($foodItem->getCookingStatus() !== FoodCookingStatusType::PACKED) {
                    throw new \Exception(sprintf('Food "%s" is not packed yet', $foodItem->getTitle()));
                }
            }
        }
        parent::handle($order);
    }

}