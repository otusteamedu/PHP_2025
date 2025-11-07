<?php

namespace App\Controller;

use App\Service\ReportService;

class CreateReport
{
    public function __construct(
        private ReportService $reportService,
    ) {
    }

    public function process(array $entryData): void
    {
        $this->reportService->addCreateReportEvent($entryData);
    }
}
