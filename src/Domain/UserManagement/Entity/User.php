<?php

declare(strict_types=1);

namespace App\Domain\UserManagement\Entity;

use App\Core\Database\Entity\AbstractEntity;

class User extends AbstractEntity
{
    public function __construct(
        private string $firstName,
        private string $lastName,
        private string $email,
        private \DateTimeImmutable $birthDate,
    ) {
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getBirthDate(): \DateTimeImmutable
    {
        return $this->birthDate;
    }

    public function getFullName(): string
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
    }

    public function getAge(): int
    {
        return new \DateTime()->diff($this->getBirthDate())->y;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'firstName' => $this->getFirstName(),
            'lastName' => $this->getLastName(),
            'fullName' => $this->getFullName(),
            'email' => $this->getEmail(),
            'birthDate' => $this->getBirthDate()->format('d.m.Y'),
            'age' => $this->getAge(),
        ];
    }
}
