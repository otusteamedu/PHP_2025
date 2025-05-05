<?php

namespace App\Application\UseCase;

use App\Application\DTO\News\CreateNewsDTO;
use App\Application\DTO\News\ResponseNewsDTO;
use App\Application\Port\NewsServiceInterface;

class CreateNewsUseCase
{
    public string $url;

    public function __construct(
        private NewsServiceInterface $newsService
    ){}

    public function execute(CreateNewsDTO $createNewsDTO):ResponseNewsDTO
    {
        return $this->newsService->createNews($createNewsDTO);
    }
}