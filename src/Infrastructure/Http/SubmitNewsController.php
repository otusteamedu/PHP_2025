<?php

namespace Infrastructure\Http;

use Application\UseCase\AddNews\SubmitNewsUseCase;
use Application\UseCase\AddNews\SubmitNewsRequest;

class SubmitNewsController
{
    public function __construct(
        private SubmitNewsUseCase $useCase,
    )
    {

    }

    public function __invoke($url): void
    {
        try {
            $SubmitNewsRequest = new SubmitNewsRequest($url);
            $submitNewsResponse = ($this->useCase)($SubmitNewsRequest);
            echo 'News ID: ' . $submitNewsResponse->id;
        } catch (\Throwable $e) {
            echo $e->getMessage();
        }
    }
}