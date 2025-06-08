<?php

declare(strict_types=1);

namespace App\Validator;

class EmailValidator
{
    public function __construct(
        private readonly EmailFormatValidator $emailFormatValidator,
        private readonly ?DnsMxRecordValidator $dnsMxRecordValidator = null,
    ) {
    }

    public function isValid(string $email): bool
    {
        $isFormatValid = $this->emailFormatValidator->isValid($email);
        if (!$isFormatValid) {
            return false;
        }

        if ($this->dnsMxRecordValidator === null) {
            return true;
        }
        $domain = substr(strrchr($email, '@'), 1);

        return $this->dnsMxRecordValidator->isValid($domain);
    }
}
