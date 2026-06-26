<?php

declare(strict_types=1);

namespace App\Core\Http\ErrorHandler;

use App\Core\Http\Message\Response;

class ApiErrorHandler implements ErrorHandlerInterface
{
    public function handle404(): Response
    {
        $data = json_encode(['error' => 'Not Found']);

        $headers = [
            'Content-Type: application/json; charset=utf-8',
        ];

        return new Response($data, ErrorHandlerInterface::HTTP_NOT_FOUND, $headers);
    }
}
