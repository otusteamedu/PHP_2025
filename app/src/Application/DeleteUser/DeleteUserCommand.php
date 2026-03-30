<?php

declare(strict_types=1);

namespace App\Application\DeleteUser;

final readonly class DeleteUserCommand
{
    public function __construct(
        public int $id,
    ){
    }
}
