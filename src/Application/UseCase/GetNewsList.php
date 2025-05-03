<?php

namespace App\Application\UseCase;

use App\Application\Port\NewsServiceInterface;

class GetNewsList
{
    public function __construct(
        private NewsServiceInterface $newsService
    ){}

    public function execute():array
    {
        return $this->newsService->getNews();
    }

}