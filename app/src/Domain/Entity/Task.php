<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use DateTimeImmutable;

final readonly class Task
{
    public function __construct(
        private string $email,
        private DateTimeImmutable $createdAt = new DateTimeImmutable()
    ) {

    }

    public function email(): string
    {
        return $this->email;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'createdAt' => $this->createdAt->format(DATE_ATOM),
        ];
    }

    /**
     * @throws \Exception
     */
    public static function fromArray(array $data): self
    {
        return new self(
            email: $data['email'],
            createdAt: new DateTimeImmutable($data['createdAt'])
        );
    }
}