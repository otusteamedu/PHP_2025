<?php

declare(strict_types=1);

namespace App\Core\Http\ErrorHandler;

use App\Core\Http\Exception\HttpExceptionInterface;
use App\Core\Http\Message\HttpStatus;
use App\Core\Http\Message\Response;

class ApiErrorHandler implements ErrorHandlerInterface
{
    public function __construct(
        private readonly ?ExceptionResponseResolverInterface $responseResolver = null,
    ) {
    }

    public function handleHttpException(HttpExceptionInterface $e): Response
    {
        return $this->errorResponse($e->getMessage(), $e->getHttpCode());
    }

    public function handleException(\Throwable $e): Response
    {
        $response = $this->responseResolver?->resolve($e);

        if ($response === null || $response->getHttpCode() >= 500) {
            $this->logServerError($e);
        }

        if ($response === null) {
            return $this->errorResponse('Internal Server Error', HttpStatus::InternalServerError->value);
        }

        return $response;
    }

    private function errorResponse(string $message, int $status): Response
    {
        return ApiErrorPayload::response($message, $status);
    }

    private function logServerError(\Throwable $e): void
    {
        error_log(sprintf('[API] %s: %s', $e::class, $e->getMessage()));

        $previous = $e->getPrevious();
        while ($previous !== null) {
            error_log(sprintf('  ↳ %s: %s', $previous::class, $previous->getMessage()));
            $previous = $previous->getPrevious();
        }
    }
}
