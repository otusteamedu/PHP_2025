<?php

namespace Alisaselezneva\Code\Domain\Services\Checkers;

class EmailFormatChecker implements EmailFormatCheckerInterface
{
    public function isValid(string $email): bool
    {
        $pattern = '/^[a-zA-Z0-9\.!#$%&\'\*+-\/=\?\^_`{|}~]{0,64}@[a-zA-Z0-9\.\-]{0,255}$/';

        if (!preg_match($pattern, $email)) {
            return false;
        }

        $parts = explode('@', $email);
        $localPart = $parts[0];
        $domain = $parts[1] ?? '';

        if (strlen($email) > 254) {
            return false;
        }

        if (strpos($email, '..') !== false) {
            return false;
        }

        if (preg_match('/^[.]|[.]$/', $localPart)) {
            return false;
        }

        if (strpos($domain, '.') === false) {
            return false;
        }

        if (preg_match('/^[.-]|[.-]$/', $domain)) {
            return false;
        }

        return $this->hasValidTld($domain);
    }

    private function hasValidTld(string $domain): bool
    {
        $tld = substr($domain, strrpos($domain, '.') + 1);

        if (preg_match('/^\d+$/', $tld)) {
            return false;
        }

        return (bool) preg_match('/[a-zA-Z]/', $tld);
    }
}
