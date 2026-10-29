<?php

declare(strict_types=1);

namespace App\Controller\Http\Api\Shared\ErrorHandling;

use App\Core\Http\ErrorHandler\ApiErrorPayload;
use App\Core\Http\Message\HttpStatus;
use App\Core\Http\Message\Response;
use App\Domain\Shared\Exception\InvalidInputException;
use App\Domain\Shared\Exception\ResourceNotFoundException;
use App\Domain\Shared\Exception\ServiceUnavailableException;

class DomainExceptionResponseResolver
{
    public function resolve(\Throwable $e): ?Response
    {
        return match (true) {
            $e instanceof ResourceNotFoundException => $this->clientResponse($e, HttpStatus::NotFound->value),
            $e instanceof InvalidInputException => $this->clientResponse($e, HttpStatus::UnprocessableContent->value),
            $e instanceof ServiceUnavailableException => $this->clientResponse($e, HttpStatus::ServiceTemporarilyUnavailable->value),
            default => null,
        };
    }

    private function clientResponse(\Throwable $e, int $httpCode): Response
    {
        return ApiErrorPayload::response($e->getMessage(), $httpCode);
    }
}
