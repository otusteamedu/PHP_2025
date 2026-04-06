<?php

namespace Pryaniki\App\Domain\ValueObjects\Email\Rules;

use Pryaniki\App\Domain\ValueObjects\Email\ValidationResult;

interface EmailRuleInterface
{
    public function validate(string $email): ValidationResult;
}