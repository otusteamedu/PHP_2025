<?php

declare(strict_types=1);

namespace App\Application\ActualizeUser;

final readonly class ActualizeUserQuery
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public string $role,
    ){
    }

}