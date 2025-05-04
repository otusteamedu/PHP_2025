<?php
declare(strict_types=1);


namespace App\Food\Domain\Builder;

use App\Food\Domain\Aggregate\Order\FoodOrder;

interface FoodOrderBuilderInterface
{
    public function reset(): FoodOrder;

    public function setFoodToOrder(): void;

    public function getOrder(): FoodOrder;

}