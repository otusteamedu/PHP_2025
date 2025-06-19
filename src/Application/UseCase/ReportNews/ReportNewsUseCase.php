<?php declare(strict_types=1);

namespace App\Application\UseCase\ReportNews;

use App\Application\Service\ReportGenerator\ReportGeneratorInterface;
use App\Application\Service\ReportGenerator\ReportGeneratorRequest;
use App\Domain\Repository\NewsRepositoryInterface;

class ReportNewsUseCase
{
    public function __construct(
        private NewsRepositoryInterface $newsRepository,
        private ReportGeneratorInterface $reportGenerator,
    ) {}

    public function __invoke(ReportNewsRequest $request): ReportNewsResponse
    {
        if (empty($request->ids)) {
            throw new InvalidReportRequestException('ID list cannot be empty');
        }

        $reportRequest = new ReportGeneratorRequest($this->newsRepository->findByIds($request->ids));
        $reportResponse = $this->reportGenerator->generate($reportRequest);

        return new ReportNewsResponse($reportResponse->path);
    }
}
