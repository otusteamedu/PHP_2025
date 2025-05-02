<?php

namespace App\Application\UseCase;

//TODO слой NewsServiceintrface на этом слое, а сервис остается на инфраструктуре
use App\Application\DTO\ResponseNewsDTO;
use App\Application\Port\NewsServiceInterface;
use App\Application\DTO\CreateNewsDTO;

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