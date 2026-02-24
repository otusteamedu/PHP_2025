<?php

namespace EmailsVerifier\Application\DTO;

use EmailsVerifier\Domain\Email;

readonly class VerificationResultDTO
{
    public function __construct(
        public Email $email,
        public bool $isValid,
        public array $errors = []
    ) {}
}
