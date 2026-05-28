<?php
namespace Pryaniki\App\Infrastructure\Services;

use Pryaniki\App\Domain\Interfaces\DomainExistenceCheckerInterface;

class DnsDomainChecker implements DomainExistenceCheckerInterface
{
    public function isExistsDomain(string $domain): bool
    {
        if (empty($domain)) {
            return false;
        }
        return checkdnsrr($domain, 'MX');
    }
}