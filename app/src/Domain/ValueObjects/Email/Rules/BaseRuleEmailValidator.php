<?php

namespace Pryaniki\App\Domain\ValueObjects\Email\Rules;

use Pryaniki\App\Domain\ValueObjects\Email\ValidationResult;

abstract class BaseRuleEmailValidator implements EmailRuleInterface
{
    protected ValidationResult $validationResult;

    public function __construct()
    {
        $this->validationResult = new ValidationResult();
    }

    abstract public function validate(string $email): ValidationResult;
}