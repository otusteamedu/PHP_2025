<?php

declare(strict_types=1);

namespace App\Core\Http\ErrorHandler;

use App\Core\Http\Message\Response;

class ApiErrorHandler implements ErrorHandlerInterface
{
    public function handle404(): Response
    {
        return new Response(
            json_encode(['error' => 'Not Found']),
            404,
            ['Content-Type: application/json; charset=utf-8'],
        );
    }
}
