<?php

declare(strict_types=1);

namespace App\Application\CreateNews;

use App\Domain\News;
use App\Domain\Repository\NewsRepositoryInterface;

class CreateNewsHandler
{
    public function __construct(
        private readonly NewsRepositoryInterface $repository,
    )
    {
    }

    public function __invoke(CreateNewsQuery $query): CreateNewsOutput
    {
        $url = $query->url;
        $title = $this->getTitle($url);

        $news = new News($url, $title);

        $this->repository->persist($news);
        $this->repository->flush();

        $id = $this->repository->getLastInsertId($news);

        return new CreateNewsOutput($id);
    }

    private function getTitle(string $url): ?string
    {
        $html = file_get_contents($url);
        preg_match('/<title>(.*?)<\/title>/si', $html, $title);

        return $title[1] ?? null;
    }
}
