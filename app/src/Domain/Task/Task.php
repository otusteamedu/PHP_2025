<?php

declare(strict_types=1);

namespace App\Domain\Task;

use DateTimeImmutable;
use Ramsey\Uuid\Uuid;

class Task
{
    private ?string $id = null;

    private string $status;
    private DateTimeImmutable $createdAt;

    public function __construct(
        private string $email,
        private string $title,
        private string $description,
    )
    {
        $this->status = Status::Send->value;
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function updateStatus(string $status): void
    {
        $this->status = $status;
    }
}
