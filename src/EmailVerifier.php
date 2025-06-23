<?php declare(strict_types=1);

namespace App\Validator;

class EmailVerifier
{
    /**
     * Проверяет список email адресов на валидность
     *
     * @param array $emails Массив email адресов для проверки
     * @return array Ассоциативный массив с результатами проверки
     */
    public function verifyEmails(array $emails): array
    {
        $results = [];

        foreach ($emails as $email) {
            $results[$email] = $this->verifyEmail($email);
        }

        return $results;
    }

    /**
     * Проверяет валидность одного email адреса
     *
     * @param string $email Email адрес для проверки
     * @return array Результат проверки с деталями
     */
    private function verifyEmail(string $email): array
    {
        // Проверка формата email регулярным выражением
        $regexValid = $this->checkWithRegex($email);

        if (!$regexValid) {
            return [
                'valid' => false,
                'reason' => 'Invalid email format',
                'regex_check' => false,
                'mx_check' => null
            ];
        }

        // Проверка MX записей домена
        $mxValid = $this->checkMxRecords($email);

        return [
            'valid' => $mxValid,
            'reason' => $mxValid ? 'Valid email' : 'No MX records found',
            'regex_check' => true,
            'mx_check' => $mxValid
        ];
    }

    /**
     * Проверяет формат email с помощью регулярного выражения
     *
     * @param string $email Email адрес для проверки
     * @return bool Результат проверки
     */
    private function checkWithRegex(string $email): bool
    {
        // Регулярное выражение для проверки email по стандарту RFC 5322
        $pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
        return (bool)preg_match($pattern, $email);
    }

    /**
     * Проверяет наличие MX записей для домена email
     *
     * @param string $email Email адрес для проверки
     * @return bool Результат проверки
     */
    private function checkMxRecords(string $email): bool
    {
        // Извлекаем домен из email
        $domain = substr(strrchr($email, "@"), 1);

        // Проверяем MX записи
        if (!checkdnsrr($domain, 'MX')) {
            return false;
        }

        // Дополнительная проверка: получаем MX записи
        $mxhosts = [];
        getmxrr($domain, $mxhosts);

        return !empty($mxhosts);
    }
}