<?php

declare(strict_types=1);

namespace App\Core\Http;

use App\Core\Http\ErrorHandler\ErrorHandlerInterface;
use App\Core\Http\Exception\HttpExceptionInterface;
use App\Core\Http\Message\Request;
use App\Core\Http\Message\Response;
use App\Core\Http\Routing\Router;

class App
{
    public function __construct(
        private readonly Request $request,
        private readonly Router $router,
        private readonly ErrorHandlerInterface $errorHandler,
    ) {
    }

    public function run(): Response
    {
        try {
            $response = $this->router->dispatch(
                $this->request->getRequestPath(),
                $this->request->getRequestMethod(),
            );
        } catch (HttpExceptionInterface $e) {
            $response = $this->errorHandler->handleHttpException($e);
        } catch (\Throwable $e) {
            $response = $this->errorHandler->handleException($e);
        }

        foreach ($response->getHeaders() as $header) {
            header($header);
        }

        http_response_code($response->getHttpCode());

        return $response;
    }
}
