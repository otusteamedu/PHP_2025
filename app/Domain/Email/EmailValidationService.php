<?php

declare(strict_types=1);

namespace App\Domain\Email;

use App\Domain\Validator\ValidatorInterface;

final class EmailValidationService
{
    /**
     * @var ValidatorInterface[]
     */
    private array $validators;

    public function __construct(array $validators = [])
    {
        $this->validators = $validators;
    }

    /**
     * Добавляет валидатор в цепочку
     */
    public function addValidator(ValidatorInterface $validator): self
    {
        $this->validators[] = $validator;

        return $this;
    }

    /**
     * Валидирует один email
     */
    public function validate(Email $email): bool
    {
        if ($email->isEmpty()) {
            return false;
        }

        foreach ($this->validators as $validator) {
            if (!$validator->isValid($email)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Валидирует список email адресов
     */
    public function validateList(array $emailStrings): array
    {
        $results = [];

        foreach ($emailStrings as $emailString) {
            $email = new Email($emailString);
            if (!$email->isEmpty()) {
                $results[$email->getValue()] = $this->validate($email);
            }
        }

        return $results;
    }
}
