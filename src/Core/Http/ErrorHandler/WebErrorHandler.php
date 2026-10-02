<?php

declare(strict_types=1);

namespace App\Core\Http\ErrorHandler;

use App\Core\Http\Exception\HttpExceptionInterface;
use App\Core\Http\Message\Response;
use App\Core\Http\View\View;

class WebErrorHandler implements ErrorHandlerInterface
{
    public function __construct(
        private readonly View $view,
    ) {
    }

    public function handleHttpException(HttpExceptionInterface $e): Response
    {
        $code = $e->getHttpCode();

        $response = match ($code) {
            ErrorHandlerInterface::HTTP_NOT_FOUND => $this->view->render('errors/404.php'),
            default => $this->view->render('errors/400.php', ['message' => $e->getMessage()]),
        };

        return $response->setHttpCode($code);
    }

    public function handleException(\Throwable $e): Response
    {
        // TODO: здесь нужно добавить логирование $e

        $response = $this->view->render('errors/500.php', [
            'message' => 'Что-то пошло не так на сервере. Мы уже знаем о проблеме.',
        ]);

        return $response->setHttpCode(ErrorHandlerInterface::HTTP_INTERNAL_SERVER_ERROR);
    }
}
