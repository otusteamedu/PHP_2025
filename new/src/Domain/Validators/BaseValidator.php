<?php

namespace EmailsVerifier\Domain\Validators;

use EmailsVerifier\Domain\Interfaces\EmailValidationInterface;
use EmailsVerifier\Domain\ValidationError;

readonly abstract class BaseValidator implements EmailValidationInterface
{
    protected function addError(string $message): ValidationError
    {
        return new ValidationError($message);
    }
}
