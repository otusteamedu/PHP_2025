<?php

declare(strict_types=1);

namespace Api\Presentation\Api\Middleware;

use Api\Presentation\Api\Helpers\Json;
use Monolog\Logger;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class ErrorMiddleware implements MiddlewareInterface
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private Logger $logger
    ) {
    }

    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        try {
            return $handler->handle($request);
        } catch (\Throwable $e) {
            $this->logger->error('Необработанная ошибка', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            $response = $this->responseFactory->createResponse();
            return Json::response($response, ['error' => 'Внутренняя ошибка сервера'], 500);
        }
    }
}
