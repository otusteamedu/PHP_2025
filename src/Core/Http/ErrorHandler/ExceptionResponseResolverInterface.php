<?php

declare(strict_types=1);

namespace App\Core\Http\ErrorHandler;

use App\Core\Http\Message\Response;

interface ExceptionResponseResolverInterface
{
    /**
     * Возвращает Response для исключений, знакомых приложению,
     * или null, если исключение приложению неизвестно.
     */
    public function resolve(\Throwable $e): ?Response;
}
