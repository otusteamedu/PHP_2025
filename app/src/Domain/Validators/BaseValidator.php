<?php

declare(strict_types=1);

namespace Pryaniki\App\Domain\Validators;

use Pryaniki\App\Domain\Interfaces\ValidatorInterface;

abstract class BaseValidator implements ValidatorInterface
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

    abstract public function validate(mixed $value, string $fieldName): bool;
}