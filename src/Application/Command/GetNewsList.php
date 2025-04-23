<?php

namespace App\Application\Command;

use App\Infrastructure\Services\NewsService;

class GetNewsList
{
    public string $url;

    public function __construct(
        private NewsService $newsService
    ){}

    public function execute()
    {
        return $this->newsService->getNews();
    }
}