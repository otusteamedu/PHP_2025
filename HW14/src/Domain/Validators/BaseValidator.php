<?php
declare(strict_types=1);

namespace App\Domain\Validators;

abstract class BaseValidator
{
    protected array $errors = [];

    public function getErrors(): array
    {
        return $this->errors;
    }

    protected function addError(string $error): void
    {
        $this->errors[] = $error;
    }

    public function resetErrors(): void
    {
        $this->errors = [];
    }

    abstract public function validate(string $email): bool;
}
