<?php

declare(strict_types=1);

namespace App\Controller\Http\Api\Shared\ErrorHandling;

use App\Controller\Http\Api\Shared\Validation\ValidationFailedException;
use App\Core\Http\ErrorHandler\ApiErrorPayload;
use App\Core\Http\ErrorHandler\ExceptionResponseResolverInterface;
use App\Core\Http\Message\HttpStatus;
use App\Core\Http\Message\Response;

class ValidationExceptionResponseResolver implements ExceptionResponseResolverInterface
{
    public function resolve(\Throwable $e): ?Response
    {
        if (!$e instanceof ValidationFailedException) {
            return null;
        }

        return ApiErrorPayload::response(
            $e->getMessage(),
            HttpStatus::UnprocessableContent->value,
            ['fields' => $e->getErrorsByField()],
        );
    }
}
