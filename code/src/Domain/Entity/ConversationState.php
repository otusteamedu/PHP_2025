<?php

declare(strict_types=1);

namespace MkdBot\Domain\Entity;

use DateTimeImmutable;
use MkdBot\Domain\Enum\ConversationStep;

/**
 * Сущность состояния многошагового диалога (одна активная сессия на пользователя)
 */
class ConversationState
{
    public function __construct(
        private int $userId,
        private ConversationStep $currentStep = ConversationStep::MainMenu,
        private array $data = [],
        private ?DateTimeImmutable $updatedAt = null,
        private ?DateTimeImmutable $expiresAt = null,
    ) {
        $this->updatedAt = $updatedAt ?? new DateTimeImmutable();
        $this->expiresAt = $expiresAt ?? new DateTimeImmutable('+30 minutes');
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getCurrentStep(): ConversationStep
    {
        return $this->currentStep;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getExpiresAt(): DateTimeImmutable
    {
        return $this->expiresAt;
    }

    /**
     * Проверяет, истекла ли сессия (TTL 30 мин)
     */
    public function isExpired(): bool
    {
        return new DateTimeImmutable() > $this->expiresAt;
    }

    /**
     * Устанавливает текущий шаг и обновляет время
     */
    public function setStep(ConversationStep $step): self
    {
        $this->currentStep = $step;
        $this->updatedAt = new DateTimeImmutable();

        return $this;
    }

    /**
     * Сохраняет данные диалога (тема, описание и т.д.)
     */
    public function setData(array $data): self
    {
        $this->data = $data;
        $this->updatedAt = new DateTimeImmutable();

        return $this;
    }

    /**
     * Добавляет/обновляет отдельный ключ в данных диалога
     */
    public function setDatum(string $key, mixed $value): self
    {
        $this->data[$key] = $value;
        $this->updatedAt = new DateTimeImmutable();

        return $this;
    }

    /**
     * Сбрасывает состояние в главное меню
     */
    public function reset(): self
    {
        $this->currentStep = ConversationStep::MainMenu;
        $this->data = [];
        $this->updatedAt = new DateTimeImmutable();
        $this->expiresAt = new DateTimeImmutable('+30 minutes');

        return $this;
    }

    /**
     * Продлевает сессию (обновляет expires_at)
     */
    public function renew(): self
    {
        $this->expiresAt = new DateTimeImmutable('+30 minutes');
        $this->updatedAt = new DateTimeImmutable();

        return $this;
    }
}
