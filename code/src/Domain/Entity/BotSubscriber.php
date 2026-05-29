<?php

declare(strict_types=1);

namespace MkdBot\Domain\Entity;

use DateTimeImmutable;

/**
 * Сущность подписчика бота — пользователь, который запустил бота
 */
class BotSubscriber
{
    public function __construct(
        private int $userId = 0,
        private string $userName = '',
        private ?DateTimeImmutable $subscribedAt = null,
        private bool $isActive = true,
        private ?DateTimeImmutable $unsubscribedAt = null,
    ) {
        $this->subscribedAt = $subscribedAt ?? new DateTimeImmutable();
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function getSubscribedAt(): DateTimeImmutable
    {
        return $this->subscribedAt;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getUnsubscribedAt(): ?DateTimeImmutable
    {
        return $this->unsubscribedAt;
    }

    /**
     * Помечает подписчика как отписавшегося (bot_stopped)
     */
    public function markAsUnsubscribed(): void
    {
        $this->isActive = false;
        $this->unsubscribedAt = new DateTimeImmutable();
    }

    /**
     * Помечает подписчика как повторно подписавшегося (bot_started повторно)
     * Обновляет subscribed_at и сбрасывает unsubscribed_at
     */
    public function markAsResubscribed(): void
    {
        $this->isActive = true;
        $this->subscribedAt = new DateTimeImmutable();
        $this->unsubscribedAt = null;
    }
}
