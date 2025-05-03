<?php
declare(strict_types=1);


namespace App\Food\Domain\Aggregate\Order;

use App\Food\Domain\Aggregate\Food;
use App\Shared\Domain\Service\UuidService;
use DateTimeImmutable;

class FoodOrder
{
    private string $id;
    private DateTimeImmutable $statusCreatedAt;
    private DateTimeImmutable $statusUpdatedAt;
    private array $foodItems = [];
    private FoodOrderStatusType $status;

    public function __construct(Food ...$food)
    {
        $this->id = UuidService::generate();
        $this->statusCreatedAt = $this->statusUpdatedAt = new DateTimeImmutable();
        $this->foodItems = $food;
        $this->status = FoodOrderStatusType::CREATED;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getFoodItems(): array
    {
        return $this->foodItems;
    }

    public function getStatus(): FoodOrderStatusType
    {
        return $this->status;
    }

    public function setStatus(FoodOrderStatusType $status): void
    {
        $this->status = $status;
        $this->statusUpdatedAt = new DateTimeImmutable();
    }

    public function addFoodItem(Food ...$foodItems): void
    {
        $this->foodItems[] = $foodItems;
    }

    public function removeFoodItem(Food ...$foodItems): void
    {
        if (in_array($foodItems, $this->foodItems, true)) {
            foreach ($foodItems as $foodItem) {
                unset($this->foodItems[$foodItem->getId()]);
            }
        }
    }

    public function getStatusCreatedAt(): DateTimeImmutable
    {
        return $this->statusCreatedAt;
    }

    public function getStatusUpdatedAt(): DateTimeImmutable
    {
        return $this->statusUpdatedAt;
    }

}