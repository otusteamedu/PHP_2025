<?php
namespace Elisad5791\Phpapp;

class DefaultMxChecker implements MxCheckerInterface
{
    public function checkMxRecord(string $domain): bool
    {
        return checkdnsrr($domain, 'MX');
    }
}