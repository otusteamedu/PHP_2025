<?php

namespace Pryaniki\App\Domain\ValueObjects\Email;

class ValidationResult
{
    private bool $isValid;
    private array $error = [];

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->isValid;
    }

    /**
     * @param bool $isValid
     */
    public function setIsValid(bool $isValid): void
    {
        $this->isValid = $isValid;
    }

    /**
     * @return array
     */
    public function getError(): array
    {
        return $this->error;
    }

    public function addError(string $error): void
    {
        $this->error[] = $error;
    }
}