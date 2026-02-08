<?php

declare(strict_types=1);

namespace App\Domain\Validators;

use App\Domain\Interfaces\ValidatorInterface;

class EmailValidator
{
    /** @var ValidatorInterface[] */
    private array $validators = [];
    private string $error = '';

    public function __construct(array $validators)
    {
        $this->validators = $validators;
    }

    public function validate(mixed $email): bool
    {
        $this->error = '';

        foreach ($this->validators as $validator) {
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
