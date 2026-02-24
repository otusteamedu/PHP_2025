<?php

namespace EmailsVerifier\Tests;

use EmailsVerifier\Domain\ValidationError;

class ValidationErrorHelper
{
    public static function getErrorMessages(array $errors): array
    {
        return array_map(fn(ValidationError $e): string => $e->getMessage(), $errors);
    }
}
