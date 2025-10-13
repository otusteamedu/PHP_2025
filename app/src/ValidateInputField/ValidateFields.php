<?php

declare(strict_types=1);

namespace App\ValidateInputField;

class ValidateFields
{
    public static function validate(array $body): ?string
    {
        $email = $body['email'] ?? null;

        $validateEmail = new ValidateEmail();
        return $validateEmail->validate($email);
    }
}