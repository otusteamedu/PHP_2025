<?php

namespace Alisaselezneva\Code\Domain\Services\Checkers;

interface MxRecordCheckerInterface
{
    public function hasMxRecords(string $email): bool;
}
