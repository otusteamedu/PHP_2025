<?php

declare(strict_types=1);

namespace App\Application\FindUser;

final readonly class FindUserOutput
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public string $password,
        public string $role,
    ){
    }
}
