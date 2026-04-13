<?php

declare(strict_types=1);

namespace App\Strategy;

final class EmailValidator
{
    public function __construct(private EmailValidationStrategy $strategy)
    {
    }

    public function setStrategy(EmailValidationStrategy $strategy): void
    {
        $this->strategy = $strategy;
    }

    public function validate(string $email): bool
    {
        return $this->strategy->isValid($email);
    }
}
