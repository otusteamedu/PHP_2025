<?php

declare(strict_types=1);

namespace App\Domain\Validators;

use App\Domain\Interfaces\EmailValidatorInterface;
use App\Domain\Validators\FormatEmailValidator;
use App\Domain\Validators\MxRecordEmailValidator;


class EmailValidator implements EmailValidatorInterface
{
    private array $fieldValidators = [];
    private string $error = '';

    public function __construct()
    {
        $this->fieldValidators = [
            new FormatEmailValidator(),
            new MxRecordEmailValidator(),
        ];
    }

    public function validate(mixed $email): bool
    {
        $this->error = '';

        foreach ($this->fieldValidators as $validator) {
            if (!$validator->validate($email, 'email')) {
                $this->error = $validator->getError();
                return false;
            }
        }

        return true;
    }

    public function getError(): string
    {
        return $this->error;
    }
}
