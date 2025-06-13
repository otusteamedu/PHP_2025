<?php

namespace App\validation;

class FormatValidationRule implements EmailValidationRule {
    public function isValid(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}