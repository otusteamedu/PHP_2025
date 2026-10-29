<?php

declare(strict_types=1);

namespace App\Core\Http\ErrorHandler;

use App\Core\Http\Exception\HttpExceptionInterface;
use App\Core\Http\Message\Response;

interface ErrorHandlerInterface
{
    public function handleHttpException(HttpExceptionInterface $e): Response;

    public function handleException(\Throwable $e): Response;
}
