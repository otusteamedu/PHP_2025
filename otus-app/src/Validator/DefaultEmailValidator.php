<?php

declare(strict_types=1);

namespace App\Validator;

class DefaultEmailValidator implements EmailValidatorInterface
{
    public function isValidEmail(string $email): bool
    {
        if (empty(trim($email))) {
            return false;
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return false;
        }

        return true;
    }
}
