<?php

declare(strict_types=1);

namespace App\Validator;

interface EmailValidatorInterface
{
    public function isValidEmail(string $email): bool;
}
