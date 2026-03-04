<?php

namespace Pryaniki\App\Application\DTO;

class ValidationResultDTO
{
    public function __construct(
        public bool $success,
        public array $error
    ) {}
}