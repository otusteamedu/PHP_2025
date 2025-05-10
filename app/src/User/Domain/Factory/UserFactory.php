<?php

declare(strict_types=1);

namespace App\User\Domain\Factory;

use App\User\Domain\Aggregate\User\User;

class UserFactory
{
    public function create(string $email, string $name): User
    {
        return new User($email, $name);
    }
}
