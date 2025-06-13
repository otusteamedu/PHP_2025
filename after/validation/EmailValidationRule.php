<?php

namespace App\validation;

interface EmailValidationRule {
    public function isValid(string $email): bool;
}
