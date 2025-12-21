<?php

namespace App\Service\Validator;

interface EmailValidatorInterface
{
    public function validate(string $email): bool;

    public function getError(): array;
}
