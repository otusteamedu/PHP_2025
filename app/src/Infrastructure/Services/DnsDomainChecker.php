<?php
namespace Pryaniki\App\Infrastructure\Services;

use Pryaniki\App\Domain\Interfaces\DomainExistenceCheckerInterface;

class DnsDomainChecker implements DomainExistenceCheckerInterface
{
    public static function isExistsDomain(string $domain): bool
    {
        return checkdnsrr($domain, 'MX');
    }
}