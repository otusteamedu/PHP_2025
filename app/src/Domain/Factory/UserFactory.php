<?php
declare(strict_types=1);


namespace App\Domain\Factory;

use App\Domain\Aggregate\User\User;

class UserFactory
{
    public function create(string $email, string $name): User
    {
        return new User($email, $name);
    }

}