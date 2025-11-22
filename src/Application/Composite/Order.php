<?php

namespace Blarkinov\Hw1500\Application\Composite;

use Blarkinov\Hw1500\Application\Gateway\DataBaseGateway;
use Blarkinov\Hw1500\Domain\Composite\OrderCostInterface;
use Blarkinov\Hw1500\Domain\Repository\FoodRepositoryInterface;
use JsonSerializable;

class Order implements JsonSerializable, OrderCostInterface
{
    public const STATUS_CREATE = "CREATE";
    public const STATUS_COOKING = "COOKING";
    public const STATUS_COMPLETE = "COMPLETE";

    private int $cost = 0;

    public function __construct(
        private FoodRepositoryInterface $foodRepository,
        ?DataBaseGateway $database = null,
        private ?int $id = null,
        private ?string $status = null,
    ) {
        $this->id = $id ?? $database->getCountOrder();
        $this->status = $status ?? self::STATUS_CREATE;
    }

    public function calculate()
    {
        $this->cost = $this->foodRepository->getCost();
    }

    public function getCost(): int
    {
        return $this->cost;
    }

    public function getFood(): FoodRepositoryInterface
    {
        return $this->foodRepository;
    }

    public function getId(): int
    {
        return $this->id;
    }
    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function jsonSerialize(): mixed
    {
        $data = ['id' => $this->id, 'food' => [], 'status' => $this->status,'cost'=>$this->getCost()];

        while ($food = $this->foodRepository->pop()) {
            $data['food'][] = basename($food::class);
        }

        return $data;
    }
}
