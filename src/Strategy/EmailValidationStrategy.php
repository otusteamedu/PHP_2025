<?php

declare(strict_types=1);

namespace App\Strategy;

interface EmailValidationStrategy
{
    public function isValid(string $email): bool;
}
