<?php

namespace App\Application\Command;

use App\Application\Services\NewsService;

class GenerateNewsReport
{
    public string $url;

    public function __construct(
        private NewsService $newsService
    ){}

    public function execute(array $arNewsIds):array
    {
        return $this->newsService->generateHtmlReport($arNewsIds);
    }
}