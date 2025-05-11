<?php

declare(strict_types=1);

namespace App\News\Application\UseCase\GenerateNewsReport;

use App\News\Application\DTO\NewsDTOTransformer;
use App\News\Domain\Repository\NewsFilter;
use App\News\Domain\Repository\NewsRepositoryInterface;
use App\News\Domain\Service\NewsReportInterface;

readonly class GenerateNewsReportUseCase
{
    public function __construct(
        private NewsReportInterface $newsReport,
        private NewsRepositoryInterface $newsRepository,
        private NewsDTOTransformer $newsDTOTransformer,
    ) {
    }

    public function __invoke(GenerateNewsReportRequest $request): GenerateNewsReportResponse
    {
        $filter = new NewsFilter();
        // todo спросить, как лучше ограничить количество айдишников, например запрос на миллион новостей?
        // поставить ограничитель в GenerateNewsReportRequest?
        $filter->setNewsIds(...$request->newsIds);
        $news = $this->newsRepository->findByFilter($filter);
        if (!$news->total) {
            throw new \Exception('News not found');
        }

        $fileName = $this->newsReport->generate($this->newsDTOTransformer->fromEntityList(...$news->items));

        return new GenerateNewsReportResponse($fileName);
    }
}
