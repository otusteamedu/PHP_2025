<?php

declare(strict_types=1);

namespace App\Application\DTO;

use App\Domain\Entity\BankStatementRequest;

final readonly class StatementRequestResponseDTO
{
    public function __construct(
        private string $id,
        private string $status,
        private string $dateRange,
        private string $telegramChatId,
        private string $createdAt,
        private ?string $processedAt = null,
        private ?string $result = null
    ) {
    }

    /**
     * Создает DTO из доменной сущности
     */
    public static function fromEntity(BankStatementRequest $request): self
    {
        return new self(
            id: $request->id(),
            status: $request->status(),
            dateRange: $request->dateRange()->format(),
            telegramChatId: $request->telegramChatId()->value(),
            createdAt: $request->createdAt()->format('Y-m-d H:i:s'),
            processedAt: $request->processedAt()?->format('Y-m-d H:i:s'),
            result: $request->result()
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
     * Возвращает статус обработки запроса
     */
    public function status(): string
    {
        return $this->status;
    }

    /**
     * Возвращает период выписки в текстовом формате
     */
    public function dateRange(): string
    {
        return $this->dateRange;
    }

    /**
     * Возвращает id Telegram чата
     */
    public function telegramChatId(): string
    {
        return $this->telegramChatId;
    }

    /**
     * Возвращает дату создания запроса
     */
    public function createdAt(): string
    {
        return $this->createdAt;
    }

    /**
     * Возвращает дату обработки запроса
     */
    public function processedAt(): ?string
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
     * Преобразует DTO в массив
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'dateRange' => $this->dateRange,
            'telegramChatId' => $this->telegramChatId,
            'createdAt' => $this->createdAt,
            'processedAt' => $this->processedAt,
            'result' => $this->result,
        ];
    }
}
