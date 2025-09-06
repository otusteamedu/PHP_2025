<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\ValueObject\Priority;
use App\Domain\ValueObject\RequestId;
use App\Domain\ValueObject\RequestStatus;
use DateTimeImmutable;
use InvalidArgumentException;

final readonly class Request
{
    public function __construct(
        private RequestId $id,
        private string $data,
        private Priority $priority,
        private RequestStatus $status,
        private DateTimeImmutable $createdAt,
        private ?DateTimeImmutable $processedAt = null,
        private ?string $result = null,
        private ?string $error = null
    ) {
    }

    /**
     * Создает новый запрос в статусе pending
     */
    public static function create(
        RequestId $id,
        string $data,
        Priority $priority
    ): self {
        return new self(
            $id,
            $data,
            $priority,
            RequestStatus::pending(),
            new DateTimeImmutable()
        );
    }

    public function id(): RequestId
    {
        return $this->id;
    }

    public function data(): string
    {
        return $this->data;
    }

    public function priority(): Priority
    {
        return $this->priority;
    }

    public function status(): RequestStatus
    {
        return $this->status;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function processedAt(): ?DateTimeImmutable
    {
        return $this->processedAt;
    }

    public function result(): ?string
    {
        return $this->result;
    }

    public function error(): ?string
    {
        return $this->error;
    }

    /**
     * Отмечает запрос как находящийся в обработке
     */
    public function markAsProcessing(): self
    {
        if (!$this->status->isPending()) {
            throw new InvalidArgumentException(
                sprintf('Cannot mark request as processing. Current status: %s', $this->status->value())
            );
        }

        return new self(
            $this->id,
            $this->data,
            $this->priority,
            RequestStatus::processing(),
            $this->createdAt,
            $this->processedAt,
            $this->result,
            $this->error
        );
    }

    /**
     * Отмечает запрос как успешно завершенный
     */
    public function markAsCompleted(string $result): self
    {
        return new self(
            $this->id,
            $this->data,
            $this->priority,
            RequestStatus::completed(),
            $this->createdAt,
            new DateTimeImmutable(),
            $result,
            $this->error
        );
    }

    /**
     * Отмечает запрос как завершенный с ошибкой
     */
    public function markAsFailed(string $error): self
    {
        return new self(
            $this->id,
            $this->data,
            $this->priority,
            RequestStatus::failed(),
            $this->createdAt,
            new DateTimeImmutable(),
            $this->result,
            $error
        );
    }

    /**
     * Проверяет, находится ли запрос в ожидании обработки
     */
    public function isPending(): bool
    {
        return $this->status->isPending();
    }

    /**
     * Проверяет, находится ли запрос в процессе обработки
     */
    public function isProcessing(): bool
    {
        return $this->status->isProcessing();
    }

    /**
     * Проверяет, успешно ли завершен запрос
     */
    public function isCompleted(): bool
    {
        return $this->status->isCompleted();
    }

    /**
     * Проверяет, завершился ли запрос с ошибкой
     */
    public function isFailed(): bool
    {
        return $this->status->isFailed();
    }
}
