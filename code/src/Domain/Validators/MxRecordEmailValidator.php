<?php

declare(strict_types=1);

namespace App\Domain\Validators;

class MxRecordEmailValidator extends BaseValidator
{
    public function validate(mixed $value, string $fieldName): bool
    {
        $this->resetErrors();

        $domain = substr(strstr($value, '@'), 1);

        if (!$domain) {
            $this->addError("Поле {$fieldName} должно содержать валидный домен");
            return false;
        }

        if (!checkdnsrr($domain, 'MX')) {
            $this->addError("Для домена в поле {$fieldName} отсутствует MX запись");
            return false;
        }

        return true;
    }
}
