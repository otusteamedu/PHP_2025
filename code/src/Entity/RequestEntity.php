<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\RequestStatus;
use DateTimeImmutable;
use DateTimeInterface;

/**
 * Сущность запроса на обработку
 */
final class RequestEntity
{
    public function __construct(
        private readonly int $id,
        private readonly string $title,
        private readonly ?string $description,
        private readonly RequestStatus $status,
        private readonly ?string $errorMessage,
        private readonly string $createdAt,
        private readonly string $updatedAt,
        private readonly ?string $processedAt,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Возвращает данные запроса для API-ответа
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'request_id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status->value,
            'status_name' => $this->status->getTitle(),
            'error_message' => $this->status === RequestStatus::Failed ? 'Ошибка при обработке запроса' : null, //делаем стандартный текст, чтобы не выводить технические детали пользователю
            'created_at' => $this->formatDate($this->createdAt),
            'updated_at' => $this->formatDate($this->updatedAt),
            'processed_at' => $this->processedAt === null ? null : $this->formatDate($this->processedAt),
        ];
    }

    private function formatDate(string $date): string
    {
        return (new DateTimeImmutable($date))->format(DateTimeInterface::ATOM);
    }
}
