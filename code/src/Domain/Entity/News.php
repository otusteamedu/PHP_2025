<?php

declare(strict_types=1);

namespace MkdBot\Domain\Entity;

use DateTimeImmutable;
use MkdBot\Domain\Enum\NewsStatus;

/**
 * Сущность новости для рассылки подписчикам бота
 */
class News
{
    public function __construct(
        private ?int $id = null,
        private string $title = '',
        private string $content = '',
        private int $priority = 0,
        private ?DateTimeImmutable $createdAt = null,
        private NewsStatus $status = NewsStatus::Pending,
    ) {
        $this->createdAt = $this->createdAt ?? new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getStatus(): NewsStatus
    {
        return $this->status;
    }

    public function isPending(): bool
    {
        return $this->status === NewsStatus::Pending;
    }

    public function isDelivering(): bool
    {
        return $this->status === NewsStatus::Delivering;
    }

    public function isDelivered(): bool
    {
        return $this->status === NewsStatus::Delivered;
    }

    public function markAsDelivering(): void
    {
        $this->status = NewsStatus::Delivering;
    }

    public function markAsDelivered(): void
    {
        $this->status = NewsStatus::Delivered;
    }
}
