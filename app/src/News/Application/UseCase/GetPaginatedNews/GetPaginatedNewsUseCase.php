<?php
declare(strict_types=1);


namespace App\News\Application\UseCase\GetPaginatedNews;

use App\News\Application\DTO\NewsDTOTransformer;
use App\News\Domain\Repository\NewsRepositoryInterface;
use App\Shared\Domain\Repository\Pager;

readonly class GetPaginatedNewsUseCase
{
    public function __construct(
        private NewsDTOTransformer      $newsDTOTransformer,
        private NewsRepositoryInterface $newsRepository,
    )
    {
    }

    public function __invoke(GetPaginatedNewsRequest $request): GetPaginatedNewsResponse
    {
        $paginator = $this->newsRepository->findByFilter($request->filter);

        $news = $this->newsDTOTransformer->fromEntityList(...$paginator->items);
        $pager = new Pager(
            $request->filter->pager->page,
            $request->filter->pager->per_page,
            $paginator->total
        );

        return new GetPaginatedNewsResponse($news, $pager);
    }

}