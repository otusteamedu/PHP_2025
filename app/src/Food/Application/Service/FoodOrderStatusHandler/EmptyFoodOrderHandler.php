<?php
declare(strict_types=1);

namespace App\Food\Application\Service\FoodOrderStatusHandler;

use App\Food\Domain\Aggregate\Order\FoodOrder;

class EmptyFoodOrderHandler extends FoodOrderStatusHandler
{
    /**
     * @throws \Exception
     */
    public function handle(FoodOrder $order): void
    {
        if ([] === $order->getFoodItems()) {
            throw new \Exception('Food items not set.');
        }
        parent::handle($order);
    }

}