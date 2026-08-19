<?php

declare(strict_types=1);

namespace App\Infrastructure\Resolver;

class DnsResolver implements DnsResolverInterface
{
    public function hasMxRecord(string $hostname): bool
    {
        return checkdnsrr($hostname, 'MX');
    }
}
