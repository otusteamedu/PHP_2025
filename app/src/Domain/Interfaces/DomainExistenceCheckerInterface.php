<?php

namespace Pryaniki\App\Domain\Interfaces;

interface DomainExistenceCheckerInterface
{
    public static function isExistsDomain(string $domain): bool;
}