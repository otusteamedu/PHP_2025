<?php

namespace App\Application\UseCase\AddUrl;

use App\Application\Gateway\InternetGatewayInterface;
use App\Application\Gateway\InternetGatewayRequest;
use App\Domain\Factory\NewsFactoryInterface;
use App\Domain\Repository\NewsRepositoryInterface;

class AddUrlUseCase
{
    public function __construct(
        private readonly NewsFactoryInterface $newsFactory,
        private readonly NewsRepositoryInterface $newsRepository,
        private readonly InternetGatewayInterface $internetGateway
    )
    {
    }
    public function __invoke(AddUrlRequest $addUrlRequest): AddUrlResponse
    {
        // Получить Title
        $internetGatewayRequest = new InternetGatewayRequest($addUrlRequest->url);
        $internetGatewayResponse = $this->internetGateway->getTitle($internetGatewayRequest);
        // Создать новость
        $news = $this->newsFactory->create($addUrlRequest->url, $internetGatewayResponse->title);
        // Сохранить новость в базу
        $this->newsRepository->save($news);
        // Вернуть результат
        return new AddUrlResponse(
            $news->getId()
        );
    }
}