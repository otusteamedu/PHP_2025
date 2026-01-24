<?php
declare(strict_types=1);

namespace App\Domain\Validators;

class EmailMxRecordValidator extends BaseValidator
{
    public function validate(string $email): bool
    {
        $this->resetErrors();

        $pos = strrpos($email, '@');
        if ($pos === false || $pos === strlen($email) - 1) {
            $this->addError('Email has no valid domain');

            return false;
        }

        $domain = substr($email, $pos + 1);

        if (!checkdnsrr($domain, 'MX')) {
            $this->addError("Email has no MX record");

            return false;
        }

        return true;
    }
}
