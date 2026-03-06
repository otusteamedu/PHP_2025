<?php
declare(strict_types=1);

namespace App\Application\DTO;

class SubmitStatementDto
{
    public function __construct(
        public readonly string $email,
        public readonly string $dateFrom,
        public readonly string $dateTo,
    ) {}
}
