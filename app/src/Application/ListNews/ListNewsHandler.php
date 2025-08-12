<?php

declare(strict_types=1);

namespace App\Application\ListNews;

use App\Domain\Repository\NewsRepositoryInterface;

class ListNewsHandler
{
    public function __construct(
        private readonly NewsRepositoryInterface $repository,
    )
    {
    }

    public function __invoke(ListNewsQuery $query): ListNewsOutput
    {
        $page = $query->page;
        $limit = $query->limit;

        $offset = (int)ceil(($page - 1) * $limit);

        $news = $this->repository->findListNews($limit, $offset);

        $responseNews = [];
        foreach ($news as $new) {
            $responseNews[] = new ListNews(
                id: $new->getId(),
                url: $new->getUrl(),
                title: $new->getTitle(),
                createdAt: $new->getCreatedAt()->format('Y-m-d H:i:s'),
            );
        }

        return new ListNewsOutput($responseNews);
    }
}
