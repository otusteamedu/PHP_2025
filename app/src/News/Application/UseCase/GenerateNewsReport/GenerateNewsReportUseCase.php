<?php

declare(strict_types=1);

namespace App\News\Application\UseCase\GenerateNewsReport;

use App\News\Application\DTO\NewsDTOTransformerInterface;
use App\News\Application\Service\NewsReportGeneratorInterface;
use App\News\Domain\Repository\NewsFilter;
use App\News\Domain\Repository\NewsRepositoryInterface;

readonly class GenerateNewsReportUseCase
{
    public function __construct(
        private NewsReportGeneratorInterface $newsReport,
        private NewsRepositoryInterface $newsRepository,
        private NewsDTOTransformerInterface $newsDTOTransformer,
    ) {
    }

    public function __invoke(GenerateNewsReportRequest $request): GenerateNewsReportResponse
    {
        $filter = new NewsFilter();
        // todo спросить, как лучше ограничить количество айдишников, например запрос на миллион новостей?
        // поставить ограничитель тут или фильтр создавать с максимальным количеством, например 100?
        $filter->setNewsIds(...$request->newsIds);
        $news = $this->newsRepository->findByFilter($filter);
        if (!$news->total) {
            throw new \Exception('News not found');
        }

        $fileName = $this->newsReport->generate($this->newsDTOTransformer->fromEntityList($news->items));

        return new GenerateNewsReportResponse($fileName);
    }
}
