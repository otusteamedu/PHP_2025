<?php

namespace Hafiz\Php2025\Validator;

class EmailValidator
{
    public static function isValidFormat(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}
