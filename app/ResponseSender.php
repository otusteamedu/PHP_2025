<?php

declare(strict_types=1);

namespace App;

final class ResponseSender
{
    /**
     * Отвечает за установку HTTP-кода ответа
     */
    public function sendResponse(int $code): void
    {
        http_response_code($code);
    }
}

