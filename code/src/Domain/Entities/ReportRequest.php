<?php

declare(strict_types=1);

namespace App\Domain\Entities;

class ReportRequest
{
    public function __construct(
        private readonly string $name,
        private readonly string $email,
        private readonly int $year,
        private readonly \DateTimeImmutable $createdAt
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'year' => $this->year,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
        ];
    }
}
