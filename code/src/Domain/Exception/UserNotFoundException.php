<?php

declare(strict_types=1);

namespace App\Domain\Exception;

class UserNotFoundException extends \RuntimeException
{
    public function __construct(int $userId)
    {
        parent::__construct(sprintf('User with ID "%d" not found.', $userId));
    }
}
