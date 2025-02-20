<?php

namespace Hafiz\Php2025\Validator;

class MXValidator
{
    public static function hasMXRecord(string $domain): bool
    {
        return checkdnsrr($domain, 'MX');
    }
}
