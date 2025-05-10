<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Controller;

use App\Shared\Infrastructure\Http\Response;

class BaseAction
{
    protected function responseError(string $message, int $httpCode = 400): Response
    {
        return new Response('error', $httpCode, null, $message);
    }

    protected function responseSuccess($data, int $httpCode = 200): Response
    {
        return new Response('success', $httpCode, $data);
    }
}
