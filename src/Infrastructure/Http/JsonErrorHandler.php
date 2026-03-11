<?php
declare(strict_types=1);

namespace App\Infrastructure\Http;

use App\Domain\Exception\NotFoundException;
use App\Domain\Exception\ValidationException;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\HttpException;
use Slim\Handlers\ErrorHandler;
use Throwable;

class JsonErrorHandler extends ErrorHandler
{
    protected function respond(): ResponseInterface
    {
        $exception = $this->exception;
        $statusCode = $this->resolveStatusCode($exception);

        $message = $statusCode >= 500
            ? 'Internal server error'
            : ($exception->getMessage() !== '' ? $exception->getMessage() : 'Internal server error');

        $response = $this->responseFactory->createResponse($statusCode);
        $response->getBody()->write((string) json_encode(['message' => $message], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));

        return $response->withHeader('Content-Type', 'application/json');
    }

    private function resolveStatusCode(Throwable $exception): int
    {
        return match (true) {
            $exception instanceof ValidationException => 400,
            $exception instanceof NotFoundException => 404,
            $exception instanceof HttpException => $exception->getCode() > 0 ? $exception->getCode() : 500,
            default => 500,
        };
    }
}
