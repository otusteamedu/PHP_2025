<?php

namespace Pryaniki\App\Application\DTO;

class ValidationEmailResponseDTO
{
    public function __construct(
        public bool $success,
        public array $error
    ) {}
}