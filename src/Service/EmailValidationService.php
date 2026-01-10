<?php declare(strict_types=1);

namespace App\Service;

class EmailValidationService
{
    public function isValidEmail(string $email): bool
    {
        return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public function isValidMxRecords(string $email): bool
    {
        $domain = substr(strrchr($email, '@'), 1);
        return checkdnsrr($domain, 'MX');
    }

    public function validate(string $email): bool
    {
        return $this->isValidEmail($email) && $this->isValidMxRecords($email);
    }
}
