<?php

namespace Alisaselezneva\Code\Domain\Services;

class VerificationResult
{
    private bool $isValid;
    private array $checks;

    public function __construct(bool $isValid, array $checks)
    {
        $this->isValid = $isValid;
        $this->checks = $checks;
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }

    public function getChecks(): array
    {
        return $this->checks;
    }

    public function toArray(): array
    {
        return [
            'is_valid' => $this->isValid,
            'checks' => $this->checks,
        ];
    }
}
