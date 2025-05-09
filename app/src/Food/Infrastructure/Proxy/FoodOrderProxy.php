<?php
declare(strict_types=1);

namespace App\Food\Infrastructure\Proxy;

use App\Food\Domain\Aggregate\Food;
use App\Food\Domain\Aggregate\Order\FoodOrder;
use App\Food\Infrastructure\Repository\FoodRepository;

class FoodOrderProxy extends FoodOrder
{
    private ?array $foodItems = null;

    public function __construct(
        private readonly FoodRepository $foodRepository,
        Food                            ...$food
    )
    {
        parent::__construct(...$food);
    }

    public function getFoodItems(): array
    {
        if ($this->foodItems === null) {
            return $this->foodRepository->getByOrderId($this->getId());
        }

        return $this->foodItems;
    }

}