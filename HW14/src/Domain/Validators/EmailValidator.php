<?php
declare(strict_types=1);

namespace App\Domain\Validators;

use App\Domain\Interfaces\EmailValidatorInterface;


class EmailValidator implements EmailValidatorInterface
{

    private array $validators = [];
    private array $errors = [];

    public function __construct(BaseValidator ...$validators)
    {
        $this->validators = $validators;
    }

    /**
     * @param string $email
     * @return bool
     */
    public function validate(string $email): bool
    {
        $this->errors = [];
        $isValid = true;

        foreach ($this->validators as $validator) {
            if (!$validator->validate($email)) {
                $isValid = false;
                $this->errors = array_merge($this->errors, $validator->getErrors());
            }
        }

        return $isValid;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
