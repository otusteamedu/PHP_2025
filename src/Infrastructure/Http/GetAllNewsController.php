<?php

namespace Infrastructure\Http;

use Application\UseCase\GetAllNews\GetAllNewsUseCase;

class GetAllNewsController
{
    public function __construct(
        private GetAllNewsUseCase $useCase,
    )
    {

    }

    public function __invoke()
    {
        try {
            $submitNewsResponse = ($this->useCase)();
            return $submitNewsResponse->getAllNews();
        } catch (\Throwable $e) {
            return $e->getMessage();
        }
    }
}