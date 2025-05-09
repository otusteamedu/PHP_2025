<?php

declare(strict_types=1);

namespace App\Food\Domain\Builder;

use App\Food\Domain\Aggregate\Food;
use App\Food\Domain\Aggregate\Order\FoodOrder;

class FoodOrderBuilder implements FoodOrderBuilderInterface
{
    private FoodOrder $order;
    private array $food;

    public function __construct()
    {
        $this->order = $this->reset();
    }

    public function addFood(Food ...$food): void
    {
        $this->food = $food;
    }

    public function reset(): FoodOrder
    {
        return $this->order = new FoodOrder();
    }

    public function setFoodToOrder(): void
    {
        $this->order->addFoodItem(...$this->food);
    }

    public function getOrder(): FoodOrder
    {
        return $this->order;
    }
}
