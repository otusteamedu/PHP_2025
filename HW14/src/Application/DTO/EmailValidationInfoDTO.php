<?php
declare(strict_types=1);

namespace App\Application\DTO;

final class EmailValidationInfoDTO
{
    public string $email;

    public bool $isValid;

    public array $errors;

    public function __construct(string $email, bool $isValid, array $errors)
    {
        $this->email = $email;
        $this->isValid = $isValid;
        $this->errors = $errors;
    }

    public function toArray(): array
    {
        return [
            'email'   => $this->email,
            'isValid' => $this->isValid,
            'errors'  => $this->errors,
        ];
    }
}
