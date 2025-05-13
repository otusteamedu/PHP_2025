<?php

namespace App\Application\UseCase;

use App\Application\DTO\Report\RequestReportDTO;
use App\Application\DTO\Report\ResponseReportDTO;
use App\Application\Port\ReportServiceInterface;
use App\Domain\Repository\NewsRepositoryInterface;

class GenerateReportUseCase
{
    public string $url;

    public function __construct(
        private ReportServiceInterface $reportService,
        private NewsRepositoryInterface $newsRepository,
    ){}

    public function execute(RequestReportDTO $requestReportDTO):ResponseReportDTO
    {
        $arNews = $this->newsRepository->getByIds($requestReportDTO->arNewsIds);

        if (empty($arNews)) {
            throw new \RuntimeException('News not found');
        }

        $fileName = 'report_of_ids';
        $htmlReport = '<ul>';
        foreach ($arNews as $news) {
            $url = $news->getUrl();
            $title = $news->getTitle();
            $fileName .= '_' . $news->getId();
            $htmlReport .= '<li><a href="' . $url . '">' . $title . '</a><li>';
        }
        $htmlReport .= '</ul>';

        $filePath = $this->reportService->dumpReport($htmlReport, $fileName);
        return new ResponseReportDTO($filePath);
    }
}