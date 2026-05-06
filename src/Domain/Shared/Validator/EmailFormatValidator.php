<?php

declare(strict_types=1);

namespace App\Domain\Shared\Validator;

class EmailFormatValidator
{
    public function isValid(string $email): bool
    {
        return $email === filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}
