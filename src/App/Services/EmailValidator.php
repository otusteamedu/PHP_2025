<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\EmailValidatorInterface;

class EmailValidator implements EmailValidatorInterface
{
    /**
     * @param string[] $emails Список email-адресов для проверки
     * @param bool $checkDns Выполнять ли проверку DNS MX записей
     * @return array Результаты проверки для каждого email
     */
    public function validate(array $emails, bool $checkDns = true): array
    {
        $results = [];

        foreach ($emails as $email) {
            $results[$email] = $this->verifyEmail($email, $checkDns);
        }

        return $results;
    }

    private function verifyEmail(string $email, bool $checkDns = true): array
    {
        $result = [
            'is_valid_format' => false,
            'is_valid_dns' => null,
            'is_valid' => false,
            'errors' => []
        ];

        if (!$this->validateFormat($email)) {
            $result['errors'][] = 'Invalid email format';
            return $result;
        }

        $result['is_valid_format'] = true;

        if ($checkDns) {
            $domain = $this->extractDomain($email);

            if (!$this->checkDnsMx($domain)) {
                $result['errors'][] = 'No MX records found for domain';
                $result['is_valid_dns'] = false;
                return $result;
            }

            $result['is_valid_dns'] = true;
        }

        $result['is_valid'] = true;
        return $result;
    }

    /**
     * Проверяет формат email с помощью регулярного выражения
     */
    private function validateFormat(string $email): bool
    {
        $pattern = '/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/';

        return (bool)preg_match($pattern, $email);
    }

    private function extractDomain(string $email): string
    {
        return substr($email, strpos($email, '@') + 1);
    }

    private function checkDnsMx(string $domain): bool
    {
        if (empty($domain)) {
            return false;
        }

        return checkdnsrr($domain, 'MX');
    }
}