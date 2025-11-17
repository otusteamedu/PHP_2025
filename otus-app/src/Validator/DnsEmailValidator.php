<?php

declare(strict_types=1);

namespace App\Validator;

class DnsEmailValidator implements EmailValidatorInterface
{
    public function isValidEmail(string $email): bool
    {
        $domain = explode('@', $email)[1];

        return checkdnsrr($domain) !== false;
    }
}
