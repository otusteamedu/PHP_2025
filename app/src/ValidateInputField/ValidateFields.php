<?php

declare(strict_types=1);

namespace User\Php2025\src\ValidateInputField;

class ValidateFields
{
    public static function validate(array $body): ?string
    {
        $email = $body['email'] ?? null;

        $validateEmail = new ValidateEmail();
        return $validateEmail->validate($email);
    }
}