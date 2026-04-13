<?php

declare(strict_types=1);

namespace App\Strategy;

final class StrictEmailValidationStrategy implements EmailValidationStrategy
{
    private const EMAIL_REGEX = '/^[a-zA-Z0-9._%+-]+@((?!.*\.\.)[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})$/';

    public function isValid(string $email): bool
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return false;
        }

        return preg_match(self::EMAIL_REGEX, $email) === 1;
    }
}
