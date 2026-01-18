<?php

declare(strict_types=1);

namespace App\Service;

use App\Interfaces\EmailValidatorInterface;

class EmailValidator implements EmailValidatorInterface
{
    private const EMAIL_REGEX = '/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}$/i';

    /**
     * Проверяет формат email адреса
     * @param string $email Email адрес 
     * @return bool true - если формат валиден, false - если не валиден
     */
    private function isFormatValid(string $email): bool
    {
        return (bool)preg_match(self::EMAIL_REGEX, $email);
    }

    /**
     * Проверяет наличие MX записи для домена email адреса
     * @param string $email Email адрес
     * @return bool true - если MX запись существует, false - если не существует
     */
    private function hasMxRecord(string $email): bool
    {
        $domain = substr(strstr($email, '@'), 1);

        if (!$domain) {
            return false;
        }

        return checkdnsrr($domain, 'MX');
    }

    public function verifyEmail(string $email): bool
    {
        $isFormatValid = $this->isFormatValid($email);

        return $isFormatValid ? $this->hasMxRecord($email) : false;
    }

    public function verifyEmails(array $emails): array
    {
        $results = [];

        foreach ($emails as $key => $email) {
            $isValid = false;

            if (is_string($email)) {
                $isValid = $this->verifyEmail($email);
            }

            $results[$key] = [
                'email' => $email,
                'is_valid' => $isValid,
            ];
        }

        return $results;
    }
}
