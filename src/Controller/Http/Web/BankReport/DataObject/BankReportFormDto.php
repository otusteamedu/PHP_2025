<?php

declare(strict_types=1);

namespace App\Controller\Http\Web\BankReport\DataObject;

readonly class BankReportFormDto
{
    public function __construct(
        public string $clientName,
        public string $dateFrom,
        public string $dateTo,
        public string $reportType,
        public string $email,
    ) {
    }
}
