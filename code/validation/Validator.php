<?php

namespace App\validation;

class Validator
{
    private DnsChecker $dns;

    public function __construct(?DnsChecker $dns = null)
    {
        $this->dns = $dns ?? new DnsChecker();
    }

    private function hasValidMxRecord(string $email): bool
    {
        $domain = explode('@', $email)[1];
        return $this->dns->hasMx($domain);
    }

    public function isValid(string $string): bool
    {
        return $this->isValidEmailFormat($string) && $this->hasValidMxRecord($string);
    }

    private function isValidEmailFormat(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}
