<?php
declare(strict_types=1);

namespace App\Service\Dto;

final readonly class VerificationResponse
{
    public function __construct(
        public string $email,
        public bool $isValid,
        public array $errors = []
    ) {}
}
