<?php

namespace App\Application\Command;

//TODO слой NewsServiceintrface на этом слое, а сервис остается на инфраструктуре
use App\Infrastructure\Services\NewsService;

class GetNewsList
{
    public string $url;

    public function __construct(
        private NewsService $newsService
    ){}

    public function execute():массив DTO (массив новостей)
    {
        return $this->newsService->getNews();
    }
}