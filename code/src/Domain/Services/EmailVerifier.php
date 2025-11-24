<?php

namespace Alisaselezneva\Code\Domain\Services;

class EmailVerifier
{
    public function verify(string $email): VerificationResult
    {
        $checks = [
            'format' => $this->checkFormat($email),
            'mx_records' => $this->checkMxRecords($email),
        ];

        $isValid = $checks['format'] && $checks['mx_records'];
        
        return new VerificationResult($isValid, $checks);
    }

    private function checkFormat(string $email): bool
    {
        // Регулярное выражение для проверки формата email
        $pattern = '/^[a-zA-Z0-9\.!#$%&\'\*+-\/=\?\^_\`{|}~]{0,64}@[a-zA-Z0-9\.\-]{0,255}$/';
        
        if (!preg_match($pattern, $email)) {
            return false;
        }

        // Дополнительные проверки
        $parts = explode('@', $email);
        $localPart = $parts[0];
        $domain = $parts[1] ?? '';

        // Проверка длины
        if (strlen($email) > 254) {
            return false;
        }

        // Проверка на две точки подряд 
        if (strpos($email, '..') !== false) {
            return false;
        }

        // Проверка, что локальная часть не начинается или не заканчивается на точку
        if (preg_match('/^[.]|[.]$/', $localPart)) {
            return false;
        }

        // Проверка, что домен содержит хотя бы одну точку
        if (strpos($domain, '.') === false) {
            return false;
        }

        // Проверка, что домен не начинается или не заканчивается на точку или дефис
        if (preg_match('/^[.-]|[.-]$/', $domain)) {
            return false;
        }

        // Проверка, что TLD не является полностью цифровым
        if (!$this->isValidTld($domain)) {
            return false;
        }

        return true;
    }

    private function isValidTld(string $domain): bool
    {
        // Извлекаем TLD (последнюю часть домена после последней точки)
        $tld = substr($domain, strrpos($domain, '.') + 1);
        
        // Проверяем, что TLD не состоит только из цифр
        if (preg_match('/^\d+$/', $tld)) {
            return false;
        }
        
        // Дополнительно: TLD должен содержать хотя бы одну букву
        if (!preg_match('/[a-zA-Z]/', $tld)) {
            return false;
        }
        
        return true;
    }

    private function checkMxRecords(string $email): bool
    {
        $domain = substr(strrchr($email, "@"), 1);

        if (empty($domain)) return false;

        return checkdnsrr($domain, 'MX');
    }

    public function massVerify(array $emails): array
    {
        $results = [];
        
        foreach ($emails as $email) {
            $results[] = $this->verify($email);
        }

        return $results;
    }
}