<?php

namespace App\Validator;

class Validator
{
    public function validateEmail(string $email): bool
    {
        if (empty($email)) {
            return false;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $domain = substr(strrchr($email, "@"), 1);
        return checkdnsrr($domain);
    }
}