<?php

namespace Application\UseCase\GetNews;

use \Domain\Factory\NewsFactoryInterface;
use \Domain\Repository\NewsRepositoryInterface;

class GetNewsUseCase
{
    public function __construct(
        private readonly NewsFactoryInterface    $newsFactory,
        private readonly NewsRepositoryInterface $newsRepository,
    )
    {
    }

    public function __invoke(GetNewsRequest $request): GetNewsResponse
    {

        // Вернуть результат
        return new GetNewsResponse(
            $this->newsRepository->findById($request->id_array)
        );
    }
}