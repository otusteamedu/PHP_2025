<?php
declare(strict_types=1);


namespace App\Food\Domain\Aggregate\Order;

use App\Food\Domain\Aggregate\Food;
use App\Shared\Domain\Service\UuidService;
use DateTimeImmutable;

class Order
{
    private string $id;
    private DateTimeImmutable $createdAt;
    private array $foodItems = [];

    public function __construct(Food ...$food)
    {
        $this->id = UuidService::generate();
        $this->createdAt = new DateTimeImmutable();
        $this->foodItems = $food;

    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getFoodItems(): array
    {
        return $this->foodItems;
    }
}