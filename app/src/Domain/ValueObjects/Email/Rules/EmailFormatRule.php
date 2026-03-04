<?php

namespace Pryaniki\App\Domain\ValueObjects\Email\Rules;

use Pryaniki\App\Domain\ValueObjects\Email\ValidationResult;

class EmailFormatRule extends BaseRuleEmailValidator
{

    public function validate(string $email): ValidationResult
    {
        $isValid = filter_var($email, FILTER_VALIDATE_EMAIL) === false;

        if (!$isValid) {
            $this->validationResult->addError('Field email is not valid');
        }

        $this->validationResult->setIsValid($isValid);

        return $this->validationResult;
    }
}