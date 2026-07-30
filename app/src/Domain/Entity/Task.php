<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Enum\TaskStatus;
use App\Domain\Exception\AppException;
use DateTimeImmutable;

final class Task
{
    public function __construct(
        private array $data,
        private int $number = 0,
        private TaskStatus $status = TaskStatus::New,
        private readonly DateTimeImmutable $createdAt = new DateTimeImmutable()
    )
    {
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function assignNumber(int $number): void
    {
        $this->number = $number;
    }

    public function getStatus(): TaskStatus
    {
        return $this->status;
    }

    /**
     * @throws AppException
     */
    public function changeStatus(TaskStatus $newStatus): void
    {
        $allowedTransitions = [
            TaskStatus::New->value => [TaskStatus::Queued],
            TaskStatus::Queued->value => [TaskStatus::Processing],
            TaskStatus::Processing->value => [
                TaskStatus::Completed,
                TaskStatus::Failed,
            ],
        ];

        $allowed = $allowedTransitions[$this->status->value] ?? [];

        if (!in_array($newStatus, $allowed, true)) {
            throw new AppException('Недопустимое изменение статуса');
        }

        $this->status = $newStatus;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function toArray(): array
    {
        return [
            'number' => $this->number,
            'data' => $this->data,
            'status' => $this->status->value,
            'createdAt' => $this->createdAt->format(DATE_ATOM),
        ];
    }

    /**
     * @throws \Exception
     */
    public static function fromArray(array $data): self
    {
        $statusCode = $data['status'];

        return new self(
            number: $data['number'],
            data: $data['data'],
            status: TaskStatus::from($statusCode),
            createdAt: new DateTimeImmutable($data['createdAt'])
        );
    }
}
