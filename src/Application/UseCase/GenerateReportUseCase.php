<?php

namespace App\Application\UseCase;

use App\Application\DTO\Report\ResponseReportDTO;
use App\Application\DTO\Report\RequestReportDTO;
use App\Application\Port\ReportServiceInterface;

class GenerateReportUseCase
{
    public string $url;

    public function __construct(
        private ReportServiceInterface $reportService
    ){}

    public function execute(RequestReportDTO $requestReportDTO):ResponseReportDTO
    {
        return $this->reportService->generateHtmlReport($requestReportDTO);
    }
}