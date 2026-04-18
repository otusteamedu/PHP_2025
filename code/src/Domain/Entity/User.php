<?php

declare(strict_types=1);

namespace App\Domain\Entity;

class User
{
    public function __construct(
        private ?int $id,
        private string $name,
        private string $email,
        private ?string $telegram_id,
        private string $gender,
        private int $weight,
        private int $height,
        private \DateTimeImmutable $born
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getTelegramId(): ?string
    {
        return $this->telegram_id;
    }

    public function getGender(): string
    {
        return $this->gender;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getBorn(): \DateTimeImmutable
    {
        return $this->born;
    }
}
