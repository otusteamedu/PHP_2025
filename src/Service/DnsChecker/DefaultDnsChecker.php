<?php
declare(strict_types=1);

namespace Dinargab\Homework5\Service\DnsChecker;

class DefaultDnsChecker implements DnsCheckerInterface
{
    public function hasMxRecords(string $domain): bool
    {
        return checkdnsrr($domain, "MX");
    }
}