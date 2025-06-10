<?php
declare(strict_types=1);

namespace Infrastructure\Factory;

use Domain\Users\Entity\User;
use Domain\Users\ValueObject\Name;
use Domain\Users\ValueObject\Email;
use Domain\Users\ValueObject\Phone;

class CommonUserFactory
{
    public function create(string $name, string $email, string $phone): User
    {
        return new User(
            new Name($name),
            new Email($email),
            new Phone($phone),
        );
    }
}
