<?php

namespace Alisaselezneva\Code\Domain\Services\Checkers;

class DnsMxRecordChecker implements MxRecordCheckerInterface
{
    public function hasMxRecords(string $email): bool
    {
        $domainPart = strrchr($email, '@');

        if ($domainPart === false) {
            return false;
        }

        $domain = substr($domainPart, 1);

        if ($domain === '') {
            return false;
        }

        return checkdnsrr($domain, 'MX');
    }
}
