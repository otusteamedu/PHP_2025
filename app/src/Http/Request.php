<?php

declare(strict_types=1);

namespace User\Php2025\src\Http;

class Request
{
    public static function getBody(): array
    {
        $body = file_get_contents('php://input');
        return json_decode($body, true);
    }
}
