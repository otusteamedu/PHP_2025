<?php

declare(strict_types=1);

namespace App\Domain;

class UserIdentityMap
{
    private array $map = [];

    public function get(int $id): ?User
    {
        return $this->map[$id] ?? null;
    }

    public function add(User $user): void
    {
        $this->map[$user->id] = $user;
    }
}
