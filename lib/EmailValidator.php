<?php

declare(strict_types=1);

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
        if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
            $details[] = 'Неверный формат email.';
            $hasErrors = true;
        } else {
            $details[] = 'Проверка формата (regex): ✅';
        }

        [, $domain] = explode('@', $email, 2);

        if (!empty($domain)) {
            if (!checkdnsrr($domain, 'A') && !checkdnsrr($domain, 'AAAA')) {
                $details[] = 'Домен не существует (нет A/AAAA записей).';
                $hasErrors = true;
            } else {
                $details[] = 'Проверка существования домена: ✅';
            }

            if (!checkdnsrr($domain, 'MX')) {
                $details[] = 'Домен не может принимать email (нет MX записей).';
                $hasErrors = true;
            } else {
                $details[] = 'Проверка почтовых серверов (MX): ✅';
            }
        }

        return [!$hasErrors, $details];
    }
}


