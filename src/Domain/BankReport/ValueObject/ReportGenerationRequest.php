<?php

declare(strict_types=1);

namespace App\Domain\BankReport\ValueObject;

readonly class ReportGenerationRequest
{
    public function __construct(
        public ReportId $reportId,
        public ClientName $clientName,
        public DateRange $dateRange,
        public ReportType $reportType,
        public Email $email,
    ) {
    }

    public static function create(
        ClientName $clientName,
        DateRange $dateRange,
        ReportType $reportType,
        Email $email,
    ): self {
        return new self(
            reportId: ReportId::generate(),
            clientName: $clientName,
            dateRange: $dateRange,
            reportType: $reportType,
            email: $email,
        );
    }
}
