<?php

namespace App\validation;

class MxValidationRule implements EmailValidationRule {
    public function isValid(string $email): bool {
        $domain = explode('@', $email)[1] ?? '';
        return checkdnsrr($domain, 'MX');
    }
}