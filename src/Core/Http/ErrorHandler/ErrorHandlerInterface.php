<?php

declare(strict_types=1);

namespace App\Core\Http\ErrorHandler;

use App\Core\Http\Exception\HttpExceptionInterface;
use App\Core\Http\Message\Response;

interface ErrorHandlerInterface
{
    public const int HTTP_NOT_FOUND = 404;
    public const int HTTP_INTERNAL_SERVER_ERROR = 500;

    /**
     * Обрабатывает HTTP-исключения (4xx) с известным кодом.
     */
    public function handleHttpException(HttpExceptionInterface $e): Response;

    /**
     * Обрабатывает любые прочие исключения (500).
     */
    public function handleException(\Throwable $e): Response;
}
