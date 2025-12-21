<?php
declare(strict_types=1);

namespace App\Service\Validator;

final class MxValidator implements EmailValidatorInterface
{
    private array $cache = [];

    public function validate(string $email): bool
    {
        $domain = $this->extractDomain($email);

        $asciiDomain = $this->normalizeDomain($domain);

        if (isset($this->cache[$asciiDomain])) {
            return $this->cache[$asciiDomain];
        }

        return $this->cache[$asciiDomain] = $this->hasDnsRecords($asciiDomain);
    }

    private function extractDomain(string $email): ?string
    {
        $atPosition = strrpos($email, '@');

        return substr($email, $atPosition + 1);
    }

    private function normalizeDomain(string $domain): string
    {
        $ascii = idn_to_ascii($domain);

        return ($ascii !== false) ? $ascii : $domain;
    }

    private function hasDnsRecords(string $domain): bool
    {
        if (checkdnsrr($domain)) {
            return true;
        }

        return checkdnsrr($domain, 'A');
    }

    public function getError(): array
    {
        return ['dns_error' => 'Для указанного домена не найдены валидные почтовые (MX) или адресные (A) записи.'];
    }
}
