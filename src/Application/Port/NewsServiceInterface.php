<?php

namespace App\Application\Port;

use App\Application\DTO\News\CreateNewsDTO;
use App\Application\DTO\News\ResponseNewsDTO;

interface NewsServiceInterface
{
    public function createNews(CreateNewsDTO $createNewsDTO): ResponseNewsDTO;
    public function getNews(): array;
    public function getHtmlByUrl(string $url, string $tag = '');
}