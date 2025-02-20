<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;

class EmailValidatorService
{
    public function __construct()
    {
    }

    public function validateEmails(array $emails): array
    {
        foreach ($emails as $email) {
            $status = $this->validateEmail($email);
            $results[] = [
                'email' => $email,
                'status' => $status
            ];
        }

        return $results;
    }

    private function validateEmail(string $email): string
    {
        // Проверка формата email
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false)
            return 'invalid_format';

        // Проверка MX-записи
        if (!$this->hasMxRecord($email)) {
            return 'no_mx_record';
        }

        return 'valid';
    }

    private function hasMxRecord(string $email): bool
    {
        $domain = substr(strrchr($email, "@"), 1);
        return checkdnsrr($domain, "MX");
    }
}