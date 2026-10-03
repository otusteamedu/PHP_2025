<?php

declare(strict_types=1);

namespace App\Domain\BankReport\ValueObject;

readonly class Report
{
    public function __construct(
        public ReportId $id,
        public string $header,
        public string $body,
    ) {
    }
}
