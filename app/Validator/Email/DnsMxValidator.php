<?php

declare(strict_types=1);

namespace App\Validator\Email;

class DnsMxValidator
{
    /**
     * Проверяет, есть ли DNS MX-запись у домена, взятого из e‑mail
     */
    public function isValid(string $email): bool
    {
        $parts = explode('@', $email);

        if (count($parts) !== 2) {
            return false;
        }

        $domain = strtolower(trim($parts[1]));

        if (function_exists('checkdnsrr')) {
            return checkdnsrr($domain, "MX");
        }

        $records = dns_get_record($domain, DNS_MX);

        return !empty($records);
    }
}

