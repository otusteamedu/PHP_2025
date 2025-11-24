<?php

declare(strict_types=1);

namespace App\UserInterface;

final readonly class HttpResponse
{
    public function __construct(
        public int $status,
        public string $body,
    ) {
        http_response_code($status);
    }
}
