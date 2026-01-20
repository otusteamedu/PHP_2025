<?php

declare(strict_types=1);

namespace App\Domain\Validators;

class FormatEmailValidator extends BaseValidator
{
    private const EMAIL_REGEX = '/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}$/i';

    public function validate(mixed $value, string $fieldName): bool
    {
        $this->resetErrors();

        if ($value === null) {
            $this->addError("Поле {$fieldName} обязательно для заполнения");
            return false;
        }

        if (!is_string($value)) {
            $this->addError("Поле {$fieldName} должно быть строкой");
            return false;
        }

        if (!preg_match(self::EMAIL_REGEX, $value)) {
            $this->addError("Поле {$fieldName} должно быть валидным email адресом");
            return false;
        }

        return true;
    }
}
