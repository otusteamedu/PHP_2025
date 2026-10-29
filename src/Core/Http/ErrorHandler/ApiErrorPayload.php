<?php

declare(strict_types=1);

namespace App\Core\Http\ErrorHandler;

use App\Core\Http\Message\Response;

class ApiErrorPayload
{
    public const string CONTENT_TYPE = 'Content-Type: application/json; charset=utf-8';
    public const int JSON_FLAGS = JSON_UNESCAPED_UNICODE;

    public static function response(string $message, int $status, array $details = []): Response
    {
        $encoded = json_encode(['success' => false, 'error' => $message] + $details, self::JSON_FLAGS);

        return new Response($encoded, $status, [self::CONTENT_TYPE]);
    }
}
