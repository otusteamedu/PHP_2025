<?php

declare(strict_types=1);

namespace App\Application\DTO;

class ReportResponseDTO
{
    public function __construct(
        public readonly bool $success,
        public readonly string $message,
        public readonly array $errors = []
    ) {}

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'errors' => $this->errors,
        ];
    }
}
