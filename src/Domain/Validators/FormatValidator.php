<?php

namespace EmailsVerifier\Domain\Validators;

use EmailsVerifier\Domain\Email;

readonly class FormatValidator extends BaseValidator
{
    public function validate(Email $email): array
    {
        $errors = [];
        $address = $email->getAddress();

        $pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
        if (!preg_match($pattern, $address)) {
            $errors[] = $this->addError('Адрес не соответствует шаблону');
        }

        if (strpos($address, '..') !== false) {
            $errors[] = $this->addError('В адресе найдены две точки подряд');
        }

        $parts = explode('@', $address);
        if (count($parts) !== 2) {
            $errors[] = $this->addError('Некорректное количество символов @');
        } else {
            [$localPart, $domain] = $parts;

            if (str_starts_with($localPart, '.') || str_ends_with($localPart, '.')) {
                $errors[] = $this->addError('Локальная часть адреса начинается или заканчивается точкой');
            }

            if (str_starts_with($domain, '.')) {
                $errors[] = $this->addError('Домен начинается с точки');
            }
        }

        return $errors;
    }
}
