<?php
declare(strict_types=1);

namespace App\Domain\Validators;

class EmailFormatValidator extends BaseValidator
{

    public function validate(string $email): bool
    {
        $this->resetErrors();

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->addError("Invalid email format");

            return false;
        }

        return true;
    }
}
