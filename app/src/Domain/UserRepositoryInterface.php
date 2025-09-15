<?php

declare(strict_types=1);

namespace App\Domain;

interface UserRepositoryInterface
{
    public function findByEmail(string $email): ?User;

    public function deleteById(int $id): void;

    public function save(User $user): void;

    public function findById(int $id): ?User;
}
