<?php

declare(strict_types=1);

namespace User\Php2025;

class Response
{
    public function createResponse(int $code, string $message): array
    {
        http_response_code($code);
        header('Content-Type: application/json');
        return ['status' => $code, 'message' => $message];
    }
}