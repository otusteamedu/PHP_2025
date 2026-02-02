<?php

declare(strict_types=1);

namespace App\Domain\Entity;

class User
{
    public function __construct(
        private int $id,
        private string $name,
        private ?string $surname,
        private ?string $role,
        private ?string $postTitle = null
    ) {
    }

    public static function fromState(array $data)
    {
        return new self(
            $data['id'],
            $data['name'],
            $data['surname'] ?? null,
            $data['role'] ?? null,
            $data['postTitle'] ?? null
        );

    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function getPostTitle(): ?string
    {
        return $this->postTitle;
    }

    /**
     * Метод содержит бизнес-логику, относящуюся к пользователю.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
