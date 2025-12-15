<?php
declare(strict_types=1);

namespace Dinargab\Homework5\Service\DnsChecker;

interface DnsCheckerInterface
{
    public function hasMxRecords(string $domain): bool;
}