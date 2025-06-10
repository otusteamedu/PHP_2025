<?php
declare(strict_types=1);

namespace Domain\Users\Factory;

use Domain\Users\Entity\User;

interface UserFactoryInterface
{
    public function create(string $name, string $email, string $phone): User;
}