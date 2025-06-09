<?php

declare(strict_types=1);

namespace App\Application\UseCase\ListNews;

use App\Application\NewsContentModifier\DummyNewsContentModifier;
use App\Application\NewsContentModifier\NewsContentModifierInterface;
use App\Domain\Entity\News;
use App\Domain\Repository\NewsRepositoryInterface;

readonly class ListNewsUseCase
{
    public function __construct(
        private NewsRepositoryInterface $newsRepository,
    )
    {
    }

    public function __invoke(
        ListNewsRequest $request,
        NewsContentModifierInterface $newsContentModifier = new DummyNewsContentModifier()
    ): array
    {
        $page = max($request->page, 1);
        $offset = ($page - 1) * $request->pageSize;

        $newsList = $this->newsRepository->findAllWithPagination($request->pageSize, $offset);

        $response = array_map(
            function (News $news) use ($newsContentModifier) {
                return new ListNewsResponse(
                    $news->getId(),
                    $news->getTitle()->getValue(),
                    $news->getAuthor()->getValue(),
                    $news->getCategory()->getName()->getValue(),
                    $newsContentModifier->modify($news->getContent()->getValue()),
                    $news->getCreatedAt()->format('Y-m-d H:i:s'),
                );
            },
            $newsList
        );

        return $response;
    }
}
