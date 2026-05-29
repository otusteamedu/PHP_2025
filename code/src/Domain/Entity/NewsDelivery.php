<?php

declare(strict_types=1);

namespace MkdBot\Domain\Entity;

use DateTimeImmutable;
use MkdBot\Domain\Enum\NewsDeliveryStatus;

/**
 * Сущность доставки новости конкретному пользователю
 */
class NewsDelivery
{
    public function __construct(
        private int $newsId = 0,
        private int $userId = 0,
        private NewsDeliveryStatus $status = NewsDeliveryStatus::Pending,
        private ?DateTimeImmutable $deliveredAt = null,
    ) {
    }

    public function getNewsId(): int
    {
        return $this->newsId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getStatus(): NewsDeliveryStatus
    {
        return $this->status;
    }

    public function getDeliveredAt(): ?DateTimeImmutable
    {
        return $this->deliveredAt;
    }

    public function isPending(): bool
    {
        return $this->status === NewsDeliveryStatus::Pending;
    }

    public function isSent(): bool
    {
        return $this->status === NewsDeliveryStatus::Sent;
    }

    public function isFailed(): bool
    {
        return $this->status === NewsDeliveryStatus::Failed;
    }

    public function markAsSent(): void
    {
        $this->status = NewsDeliveryStatus::Sent;
    }

    public function markAsFailed(): void
    {
        $this->status = NewsDeliveryStatus::Failed;
    }
}
