<?php

declare(strict_types=1);

namespace App\Infrastructure\Resolver;

interface DnsResolverInterface
{
    public function hasMxRecord(string $hostname): bool;
}
