<?php

namespace Pryaniki\App\Domain\ValueObjects\Email\Rules;

class NotEmptyRule extends BaseRuleEmailValidator
{
    protected function applyRule(string $email): bool
    {
        return $email !== '';
    }

    protected function getValidationErrorMessage(string $email): string
    {
        return 'Field email is empty';
    }
}