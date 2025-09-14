<?php

declare(strict_types=1);

namespace App\Domain;

use DateTimeImmutable;

class User
{
    public function __construct(
        public ?int   $id,
        public string $name,
        public string $email,
        public string $password,
        public string $role,
        public DateTimeImmutable $createdAt,
        public DateTimeImmutable $updatedAt,
    ){
    }

    public static function create(string $name, string $email, string $password, string $role): self
    {
        return new self(
            null, $name, $email, $password, $role, new DateTimeImmutable(), new DateTimeImmutable());
    }

    public function update(string $name, string $email, string $password, string $role): void
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
        $this->updatedAt = new DateTimeImmutable();
    }
}
