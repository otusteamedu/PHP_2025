<?php

namespace Pryaniki\App\Domain\Interfaces;

interface ValidatorInterface
{
    public function validate(mixed $value, string $fieldName): bool;
}