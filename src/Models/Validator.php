<?php

namespace App\Models;

use App\Http\Response;

class Validator
{
    public static function validateString($string)
    {
        if (empty($string)) {
            throw new \Exception("Empty String");
        }

        // Проверка, что строка не начинается с закрывающей скобки
        if ($string[0] === ')') {
            throw new \Exception("First - ')'");
        }

        $openCount = substr_count($string, '(');
        $closeCount = substr_count($string, ')');

        if ($openCount !== $closeCount) {
            throw new \Exception("Incorrect String");
        }

        return true;
    }

    public static function checkEmails(?array $emails): array
    {
        $results = [];
        if (empty($emails)) throw new \Exception("Empty emails");

        foreach ($emails as $email) {
            $status = self::checkEmail($email);
            $results[] = [
                'email' => $email,
                'status' => $status
            ];
        }

        return $results;
    }

    private static function checkEmail(string $email): string
    {
        // Проверка формата email
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false)
            return 'invalid_format';

        // Проверка MX-записи
        if (!self::hasMxRecord($email)) {
            return 'no_mx_record';
        }

        return 'valid';
    }

    private static function hasMxRecord(string $email): bool
    {
        $domain = substr(strrchr($email, "@"), 1);
        return checkdnsrr($domain, "MX");
    }
}
