<?php

declare(strict_types=1);

namespace App\Domain\DTO;

readonly class EmailValidationResult
{
    public function __construct(
        public mixed $email,
        public bool $isValid
    ) {}

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'is_valid' => $this->isValid,
        ];
    }
}
