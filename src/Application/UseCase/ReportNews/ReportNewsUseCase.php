<?php declare(strict_types=1);

namespace App\Application\UseCase\ReportNews;

use App\Domain\Repository\NewsRepositoryInterface;
use App\Application\Service\ReportGeneratorInterface;

class ReportNewsUseCase
{
    public function __construct(
        private NewsRepositoryInterface $newsRepository,
        private ReportGeneratorInterface $reportGenerator,
        private string $reportsDir,
    ) {}

    public function __invoke(ReportNewsRequest $request): ReportNewsResponse
    {
        if (empty($request->ids)) {
            throw new InvalidReportRequestException('ID list cannot be empty');
        }

        $newsList = $this->newsRepository->findByIds($request->ids);
        $html = $this->reportGenerator->generate($newsList);

        if (!is_dir($this->reportsDir)) {
            mkdir($this->reportsDir, 0777, true);
        }

        $filename = 'report_' . time() . '.html';
        $fullPath = $this->reportsDir . '/' . $filename;

        $this->reportGenerator->saveToFile($html, $fullPath);

        return new ReportNewsResponse($fullPath);
    }
}
