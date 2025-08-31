<?php

declare(strict_types=1);

namespace Dinargab\Homework5\Result;

class EmailValidationResult
{
    private string $email;
    private bool $isValid;
    private ?string $error = null;

    public function __construct(string $email, bool $isValid, ?string $error = null)
    {
        $this->email = $email;
        $this->isValid = $isValid;
        $this->error = $error;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    /**
     * Возвращает результат в виде ассоциативного массива
     *
     * @return array{email: string, valid: bool, error: string|null}
     */
    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'valid' => $this->isValid,
            'error' => $this->error,
        ];
    }
}