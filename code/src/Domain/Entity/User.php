<?php

declare(strict_types=1);

namespace App\Domain\Entity;

class User
{
    public function __construct(
        private ?int $id,
        private string $name,
        private string $email,
        private ?int $telegram_id,
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

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getTelegramId(): ?int
    {
        return $this->telegram_id;
    }

    public function setTelegramId( $telegram_id): void
    {
        $this->telegram_id = (int)$telegram_id;
    }

    public function getGender(): string
    {
        return $this->gender;
    }

    public function setGender(string $gender): void
    {
        $this->gender = $gender;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    public function setWeight(int $weight): void
    {
        $this->weight = $weight;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function setHeight(int $height): void
    {
        $this->height = $height;
    }

    public function getBorn(): \DateTimeImmutable
    {
        return $this->born;
    }

    public function setBorn(\DateTimeImmutable $born): void
    {
        $this->born = $born;
    }
}
