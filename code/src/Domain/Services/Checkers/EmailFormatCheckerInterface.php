<?php

namespace Alisaselezneva\Code\Domain\Services\Checkers;

interface EmailFormatCheckerInterface
{
    public function isValid(string $email): bool;
}
