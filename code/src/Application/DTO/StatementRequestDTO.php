<?php

declare(strict_types=1);

namespace Queues\Application\DTO;

class StatementRequestDTO
{
    public function __construct(
        public readonly string $dateFrom,
        public readonly string $dateTo,
        public readonly string $email
    ) {
    }
}
