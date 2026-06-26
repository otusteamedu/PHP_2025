<?php

declare(strict_types=1);

namespace App\Core\Http\ErrorHandler;

use App\Core\Http\Message\Response;

interface ErrorHandlerInterface
{
    public const int HTTP_NOT_FOUND = 404;

    public function handle404(): Response;
}
