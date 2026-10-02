<?php

declare(strict_types=1);

namespace App\Core\Http\ErrorHandler;

use App\Core\Http\Exception\HttpExceptionInterface;
use App\Core\Http\Message\Response;

class ApiErrorHandler implements ErrorHandlerInterface
{
    public function handleHttpException(HttpExceptionInterface $e): Response
    {
        $data = json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);

        return new Response(
            $data,
            $e->getHttpCode(),
            ['Content-Type: application/json; charset=utf-8'],
        );
    }

    public function handleException(\Throwable $e): Response
    {
        $data = json_encode([
            'success' => false,
            'error' => 'Internal Server Error',
        ], JSON_UNESCAPED_UNICODE);

        return new Response(
            $data,
            ErrorHandlerInterface::HTTP_INTERNAL_SERVER_ERROR,
            ['Content-Type: application/json; charset=utf-8'],
        );
    }
}
