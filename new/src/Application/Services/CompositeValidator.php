<?php

namespace EmailsVerifier\Application\Services;

use EmailsVerifier\Application\Interfaces\ValidationStrategyInterface;
use EmailsVerifier\Domain\Email;

readonly class CompositeValidator implements ValidationStrategyInterface
{
    public function __construct(
        private array $strategies
    ) {}

    public function validate(Email $email): array
    {
        $allErrors = [];
        foreach ($this->strategies as $strategy) {
            $errors = $strategy->validate($email);
            $allErrors = array_merge($allErrors, $errors);
        }
        return $allErrors;
    }
}
