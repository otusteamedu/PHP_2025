<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Domain\Enums\OrderStatus;
use App\Domain\Interfaces\ProductInterface;
use DateTimeImmutable;
use InvalidArgumentException;

class Order
{
    private string $id;
    private OrderStatus $status;
    private DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $updatedAt = null;

    /**
     * @var ProductInterface[]
     */
    private array $items = [];

    /**
     * История изменений статуса
     * @var array<array{status: string, timestamp: string, description: string}>
     */
    private array $statusHistory = [];

    public function __construct(string $id)
    {
        $this->id = $id;
        $this->status = OrderStatus::CREATED;
        $this->createdAt = new DateTimeImmutable();
        $this->addStatusToHistory($this->status);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @return ProductInterface[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function addItem(ProductInterface $product): self
    {
        $this->items[] = $product;
        return $this;
    }

    public function getTotalPrice(): float
    {
        return array_reduce(
            $this->items,
            fn(float $total, ProductInterface $item) => $total + $item->getPrice(),
            0.0
        );
    }

    public function setStatus(OrderStatus $newStatus): self
    {
        if (!$this->status->canTransitionTo($newStatus)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Невозможно перейти из статуса "%s" в "%s"',
                    $this->status->getDescription(),
                    $newStatus->getDescription()
                )
            );
        }

        $this->status = $newStatus;
        $this->updatedAt = new DateTimeImmutable();
        $this->addStatusToHistory($newStatus);

        return $this;
    }

    public function getStatusHistory(): array
    {
        return $this->statusHistory;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status->value,
            'status_description' => $this->status->getDescription(),
            'items' => array_map(fn(ProductInterface $item) => $item->toArray(), $this->items),
            'total_price' => $this->getTotalPrice(),
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'status_history' => $this->statusHistory,
        ];
    }

    private function addStatusToHistory(OrderStatus $status): void
    {
        $this->statusHistory[] = [
            'status' => $status->value,
            'timestamp' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            'description' => $status->getDescription(),
        ];
    }
}
