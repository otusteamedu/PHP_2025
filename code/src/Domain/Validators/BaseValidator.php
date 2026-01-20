<?php

declare(strict_types=1);

namespace App\Domain\Validators;

use App\Domain\Interfaces\ValidatorInterface;

abstract class BaseValidator implements ValidatorInterface
{
    protected string $error = '';

    public function getError(): string
    {
        return $this->error;
    }

    protected function addError(string $error): void
    {
        $this->error = $error;
    }

    public function resetErrors(): void
    {
        $this->error = '';
    }

    abstract public function validate(mixed $value, string $fieldName): bool;
}
