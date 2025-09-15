<?php

declare(strict_types=1);

namespace App\UserInterface\Api\User\Request;

 class ActualizeUserRequest
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public string $role,
    )
    {
    }
}