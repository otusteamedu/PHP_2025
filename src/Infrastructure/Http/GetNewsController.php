<?php

namespace Infrastructure\Http;

use Application\UseCase\GetNews\GetNewsUseCase;
use Application\UseCase\GetNews\GetNewsRequest;

class GetNewsController
{
    public function __construct(
        private GetNewsUseCase $useCase,
    )
    {

    }

    public function __invoke($id_array)
    {
        try {
            $getNewsRequest = new GetNewsRequest($id_array);
            $getNewsResponse = ($this->useCase)($getNewsRequest);
            return $getNewsResponse->getNews();
        } catch (\Throwable $e) {
            return $e->getMessage();
        }
    }
}