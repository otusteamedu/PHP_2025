<?php

namespace Application\UseCase\AddNews;

use \Domain\Factory\NewsFactoryInterface;
use \Domain\Repository\NewsRepositoryInterface;

class SubmitNewsUseCase
{
    public function __construct(
        private readonly NewsFactoryInterface    $newsFactory,
        private readonly NewsRepositoryInterface $newsRepository,
    )
    {
    }

    public function __invoke(SubmitNewsRequest $request): SubmitNewsResponse
    {
        // Создать новость
        $url = $this->newsFactory->create($request->url);

        // Сохранить новость в базу
        $this->newsRepository->save($url);

        // Вернуть результат
        return new SubmitNewsResponse(
            $url->getId()
        );
    }
}