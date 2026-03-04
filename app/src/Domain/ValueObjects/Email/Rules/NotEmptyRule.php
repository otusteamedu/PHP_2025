<?php

namespace Pryaniki\App\Domain\ValueObjects\Email\Rules;

use Pryaniki\App\Domain\ValueObjects\Email\ValidationResult;

class NotEmptyRule extends BaseRuleEmailValidator
{

    public function validate(string $email): ValidationResult
    {
        $isValid = $email !== '';

        if (!$isValid) {
            $this->validationResult->addError('Field email is empty');
        }

        $this->validationResult->setIsValid($isValid);

        return $this->validationResult;
    }
}