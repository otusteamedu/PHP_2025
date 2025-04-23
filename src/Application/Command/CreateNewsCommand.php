<?php

namespace App\Application\Command;

use App\Infrastructure\Services\NewsService;

class CreateNewsCommand
{
    public string $url;

    public function __construct(
        private NewsService $newsService
    ){}

    public function execute(string $url)
    {
        return $this->newsService->createNews($url);
    }
}