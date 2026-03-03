<?php

declare(strict_types=1);

namespace App\Application\GetBankStatement;

use DateTimeImmutable;

final readonly class GetBankStatementRequest
{
    public function __construct(
        public string $account,
        public DateTimeImmutable $dateFrom,
        public DateTimeImmutable $dateTo,
    ) {
    }
}
