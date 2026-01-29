<?php

namespace EmailsVerifier\Infrastructure;

use EmailsVerifier\Domain\Interfaces\MxCheckerInterface;

class MxChecker implements MxCheckerInterface
{
    public function hasMxRecord(string $domain): bool
    {
        return checkdnsrr($domain, 'MX');
    }
}
