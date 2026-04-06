<?php

namespace Pryaniki\App\Application\DTO;

class ValidateEmailRequestDTO
{
    public function __construct(
        public string $email
    ) {}
}