<?php
declare(strict_types=1);

namespace App\Food\Application\Service\FoodOrderStatusHandler;

use App\Food\Domain\Aggregate\FoodCookingStatusType;
use App\Food\Domain\Aggregate\Order\FoodOrder;
use App\Food\Domain\Aggregate\Order\FoodOrderStatusType;

class CompletedFoodOrderStatusHandler extends FoodOrderStatusHandler
{
    /**
     * @throws \Exception
     */
    public function handle(FoodOrder $order): void
    {
        if ($order->getStatus() === FoodOrderStatusType::COOKING_COMPLETED) {
            foreach ($order->getFoodItems() as $foodItem) {
                if ($foodItem->getCookingStatus() !== FoodCookingStatusType::FINISHED) {
                    throw new \Exception(sprintf('Food "%s" is not ready yet', $foodItem->getTitle()));
                }
            }
        }
        parent::handle($order);
    }

}