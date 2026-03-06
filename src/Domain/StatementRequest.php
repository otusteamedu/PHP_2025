<?php
declare(strict_types=1);

namespace App\Domain;

use Ramsey\Uuid\Uuid;

class StatementRequest
{
    private function __construct(
        public readonly string $id,
        public readonly string $email,
        public readonly string $dateFrom,
        public readonly string $dateTo,
        public readonly \DateTimeImmutable $createdAt,
    ) {}

    public static function create(string $email, string $dateFrom, string $dateTo): self
    {
        return new self(
            id: Uuid::uuid4()->toString(),
            email: $email,
            dateFrom: $dateFrom,
            dateTo: $dateTo,
            createdAt: new \DateTimeImmutable('now')
        );
    }

    public function toArray(): array
    {
        return [
            'request_id' => $this->id,
            'email' => $this->email,
            'date_from' => $this->dateFrom,
            'date_to' => $this->dateTo,
            'created_at' => $this->createdAt->format(DATE_ATOM),
        ];
    }
}
