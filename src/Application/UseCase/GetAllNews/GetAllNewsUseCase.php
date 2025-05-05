<?php

namespace Application\UseCase\GetAllNews;

use \Domain\Factory\NewsFactoryInterface;
use \Domain\Repository\NewsRepositoryInterface;

class GetAllNewsUseCase
{
    public function __construct(
        private readonly NewsFactoryInterface    $newsFactory,
        private readonly NewsRepositoryInterface $newsRepository,
    )
    {
    }

    public function __invoke(): GetAllNewsResponse
    {

        // Вернуть результат
        return new GetAllNewsResponse(
            $this->newsRepository->findAll()
        );
    }
}