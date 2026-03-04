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

    public function validate(string $email): ValidationResult
    {
        $isValid = $this->applyRule($email);

        if (!$isValid) {
            $this->validationResult->addError('Field email is not valid');
        }

        $this->validationResult->setIsValid($isValid);

        return $this->validationResult;
    }

    abstract protected function applyRule(string $email): bool;
    abstract protected function getValidationErrorMessage(string $email): string;

}