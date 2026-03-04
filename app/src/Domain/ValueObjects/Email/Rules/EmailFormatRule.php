<?php

namespace Pryaniki\App\Domain\ValueObjects\Email\Rules;

class EmailFormatRule extends BaseRuleEmailValidator
{
    protected function applyRule(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    protected function getValidationErrorMessage(string $email): string
    {
        return 'Field email "' . $email . '" is not valid';
    }
}