<?php

namespace EmailsVerifier\Domain\Interfaces;

interface MxCheckerInterface
{
    public function hasMxRecord(string $domain): bool;
}
