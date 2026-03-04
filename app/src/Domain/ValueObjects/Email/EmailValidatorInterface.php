<?php

namespace Pryaniki\App\Domain\ValueObjects\Email;

use Pryaniki\App\Domain\ValueObjects\Email\ValidationResult;

interface EmailValidatorInterface
{
    public function validate(string $email): ValidationResult;
}