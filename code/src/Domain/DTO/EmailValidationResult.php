<?php

declare(strict_types=1);

namespace App\Domain\DTO;

readonly class EmailValidationResult
{
    public function __construct(
        public mixed $email,
        public bool $isValid,
        public string $error = ''
    ) {}

    public function toArray(): array
    {
        $result = [
            'email' => $this->email,
            'is_valid' => $this->isValid,
        ];

        if ($this->error !== '') {
            $result['error'] = $this->error;
        }

        return $result;
    }
}
