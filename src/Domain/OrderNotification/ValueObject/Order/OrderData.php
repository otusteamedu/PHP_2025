<?php

declare(strict_types=1);

namespace App\Domain\OrderNotification\ValueObject\Order;

readonly class OrderData
{
    public function __construct(
        private string $customerName,
        private Money $total,
        private int $itemsCount,
        private ?string $trackingNumber,
    ) {
    }

    public function getCustomerName(): string { return $this->customerName; }
    public function getTotal(): Money { return $this->total; }
    public function getItemsCount(): int { return $this->itemsCount; }
    public function getTrackingNumber(): ?string { return $this->trackingNumber; }

    public function requireTrackingNumber(): string
    {
        if ($this->trackingNumber === null) {
            throw new \LogicException('Трек-номер отсутствует для отправленного заказа');
        }

        return $this->trackingNumber;
    }

    public function toArray(): array
    {
        return [
            'customerName' => $this->getCustomerName(),
            'total' => $this->getTotal()->format(),
            'itemsCount' => $this->getItemsCount(),
            'trackingNumber' => $this->getTrackingNumber(),
        ];
    }
}
