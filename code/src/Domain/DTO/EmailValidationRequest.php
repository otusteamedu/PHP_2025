<?php

declare(strict_types=1);

namespace App\Domain\DTO;

readonly class EmailValidationRequest
{
    public function __construct(
        public array $emails
    ) {}
}
