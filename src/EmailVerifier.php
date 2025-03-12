<?php

declare(strict_types=1);

namespace App;

class EmailVerifier
{
    public function verify(string $email): string
    {
        if (!$this->isValidFormat($email)) {
            return "$email - невалидный";
        }

        if (!$this->hasMXRecord($email)) {
            return "$email - нет MX-записи";
        }

        return "$email - валиден";
    }

    private function isValidFormat(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    private function hasMXRecord(string $email): bool
    {
        $domain = substr(strrchr($email, "@"), 1);
        return checkdnsrr($domain, "MX");
    }
}