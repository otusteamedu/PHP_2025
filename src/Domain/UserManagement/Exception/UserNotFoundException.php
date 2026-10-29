<?php

declare(strict_types=1);

namespace App\Domain\UserManagement\Exception;

use App\Domain\Shared\Exception\ResourceNotFoundException;

class UserNotFoundException extends ResourceNotFoundException
{
    public static function byId(int $id): self
    {
        return new self(sprintf("Пользователь с id=%s не найден.", $id), 404);
    }
}
