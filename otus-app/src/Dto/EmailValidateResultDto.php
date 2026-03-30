<?php

declare(strict_types=1);

namespace App\Dto;

class EmailValidateResultDto
{
    public function __construct(
        public array $validEmailList = [],
        public array $invalidEmailList = [],
        public bool $isValidEmailList = false,
    ) {
    }
}
