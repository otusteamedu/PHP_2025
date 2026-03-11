<?php
declare(strict_types=1);

namespace App\Domain\Task\Entity;

use App\Domain\Task\Enum\TaskStatus;
use App\Domain\Task\ValueObject\TaskId;
use DateTimeImmutable;

class Task
{
    /**
     * @param array<string, mixed> $payload
     */
    public function __construct(
        private TaskId $id,
        private TaskStatus $status,
        private array $payload,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt,
    ) {}

    public function getId(): TaskId
    {
        return $this->id;
    }

    public function getStatus(): TaskStatus
    {
        return $this->status;
    }

    /**
     * @return array<string, mixed>
     */
    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
