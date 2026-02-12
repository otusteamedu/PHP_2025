<?php

declare(strict_types=1);

namespace App;

class EmailValidator
{
    /**
     * @return array{0: bool, 1: array<int,string>}
     */
    public function validate(string $email): array
    {
        $details = [];
        $hasErrors = false;

        // Проверка формата email
        [$formatIsValid, $formatDetails] = $this->validateFormat($email);
        $details[] = $formatDetails;
        if (!$formatIsValid) {
            $hasErrors = true;
        }

        if ($formatIsValid) {
            [, $domain] = explode('@', $email, 2);

            // Проверка существования домена
            [$domainExists, $domainDetails] = $this->validateDomainExistence($domain);
            $details[] = $domainDetails;
            if (!$domainExists) {
                $hasErrors = true;
            }

            // Проверка MX записей
            [$mxRecordsExist, $mxDetails] = $this->validateMxRecords($domain);
            $details[] = $mxDetails;
            if (!$mxRecordsExist) {
                $hasErrors = true;
            }
        }


        return [!$hasErrors, $details];
    }

    /**
     * Проверяет формат email с помощью регулярного выражения.
     * @return array{0: bool, 1: string}
     */
    private function validateFormat(string $email): array
    {
        if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
            return [false, 'Неверный формат email.'];
        }
        return [true, 'Проверка формата (regex): ✅'];
    }

    /**
     * Проверяет существование домена email.
     * @return array{0: bool, 1: string}
     */
    private function validateDomainExistence(string $domain): array
    {
        if (empty($domain)) {
            return [false, 'Домен не может быть пустым.'];
        }
        if (!checkdnsrr($domain, 'A') && !checkdnsrr($domain, 'AAAA')) {
            return [false, 'Домен не существует (нет A/AAAA записей).'];
        }
        return [true, 'Проверка существования домена: ✅'];
    }

    /**
     * Проверяет наличие MX записей для домена email.
     * @return array{0: bool, 1: string}
     */
    private function validateMxRecords(string $domain): array
    {
        if (empty($domain)) {
            return [false, 'Домен не может быть пустым для проверки MX записей.'];
        }
        if (!checkdnsrr($domain, 'MX')) {
            return [false, 'Домен не может принимать email (нет MX записей).'];
        }
        return [true, 'Проверка почтовых серверов (MX): ✅'];
    }
}