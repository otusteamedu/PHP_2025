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

    public function __invoke(SubmitNewsRequest $request)
    {
        try {
            $response = ($this->useCase)($request);
            $answewr = "Ok- ".$response." -";
        } catch (\Throwable $e) {
            $answewr = "Ошибка ";
        }
        return $answewr;
    }
}