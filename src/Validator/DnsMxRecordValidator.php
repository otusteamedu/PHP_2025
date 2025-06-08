<?php

declare(strict_types=1);

namespace App\Validator;

class DnsMxRecordValidator
{
    public function isValid(string $hostname): bool
    {
        $hostname = idn_to_ascii($hostname);
        if ($hostname === false) {
            return false;
        }

        return checkdnsrr($hostname);
    }
}
