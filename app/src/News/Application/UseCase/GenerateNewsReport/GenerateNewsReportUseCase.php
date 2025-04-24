<?php

declare(strict_types=1);

namespace App\News\Application\UseCase\GenerateNewsReport;

use App\News\Application\Service\NewsReportInterface;

readonly class GenerateNewsReportUseCase
{
    public function __construct(
        private NewsReportInterface $newsReport,
    ) {
    }

    public function __invoke(GenerateNewsReportRequest $request): GenerateNewsReportResponse
    {
        $fileName = $this->newsReport->generate(...$request->newsIds);

        return new GenerateNewsReportResponse($fileName);
    }

}
