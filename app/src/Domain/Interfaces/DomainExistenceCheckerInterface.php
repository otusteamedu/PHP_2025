<?php

namespace Pryaniki\App\Domain\Interfaces;

interface DomainExistenceCheckerInterface
{
    public function isExistsDomain(string $domain): bool;
}