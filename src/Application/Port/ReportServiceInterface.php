<?php

namespace App\Application\Port;

use App\Application\DTO\Report\RequestReportDTO;
use App\Application\DTO\Report\ResponseReportDTO;

interface ReportServiceInterface
{
    public function generateHtmlReport(RequestReportDTO $requestReportDTO):ResponseReportDTO;

    public function dumpReport(string $htmlReport, string $fileName):string;
}