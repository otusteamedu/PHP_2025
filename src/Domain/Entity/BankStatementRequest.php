<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\ValueObject\DateRange;
use App\Domain\ValueObject\TelegramChatId;
use DateTimeImmutable;

final readonly class BankStatementRequest
{
    public function __construct(
        private string $id,
        private DateRange $dateRange,
        private TelegramChatId $telegramChatId,
        private DateTimeImmutable $createdAt,
        private string $status,
        private ?DateTimeImmutable $processedAt = null,
        private ?string $result = null
    ) {
    }

    /**
     * Создает новый запрос в статусе pending
     */
    public static function create(
        string $id,
        DateRange $dateRange,
        TelegramChatId $telegramChatId
    ): self {
        return new self(
            $id,
            $dateRange,
            $telegramChatId,
            new DateTimeImmutable(),
            'pending'
        );
    }

    /**
     * Возвращает id запроса
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * Возвращает диапазон дат для выписки
     */
    public function dateRange(): DateRange
    {
        return $this->dateRange;
    }

    /**
     * Возвращает id Telegram чата для уведомлений
     */
    public function telegramChatId(): TelegramChatId
    {
        return $this->telegramChatId;
    }

    /**
     * Возвращает текущий статус обработки запроса
     */
    public function status(): string
    {
        return $this->status;
    }

    /**
     * Возвращает дату и время создания запроса
     */
    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Возвращает дату и время завершения обработки запроса
     */
    public function processedAt(): ?DateTimeImmutable
    {
        return $this->processedAt;
    }

    /**
     * Возвращает результат обработки запроса
     */
    public function result(): ?string
    {
        return $this->result;
    }

    /**
     * Отмечает запрос как находящийся в обработке
     */
    public function markAsProcessing(): self
    {
        return new self(
            $this->id,
            $this->dateRange,
            $this->telegramChatId,
            $this->createdAt,
            'processing',
            $this->processedAt,
            $this->result
        );
    }

    /**
     * Отмечает запрос как успешно завершенный
     */
    public function markAsCompleted(string $result): self
    {
        return new self(
            $this->id,
            $this->dateRange,
            $this->telegramChatId,
            $this->createdAt,
            'completed',
            new DateTimeImmutable(),
            $result
        );
    }

    /**
     * Отмечает запрос как завершенный с ошибкой
     */
    public function markAsFailed(string $error): self
    {
        return new self(
            $this->id,
            $this->dateRange,
            $this->telegramChatId,
            $this->createdAt,
            'failed',
            new DateTimeImmutable(),
            $error
        );
    }

    /**
     * Проверяет, находится ли запрос в ожидании обработки
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Проверяет, находится ли запрос в процессе обработки
     */
    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    /**
     * Проверяет, успешно ли завершен запрос
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Проверяет, завершился ли запрос с ошибкой
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }
}
