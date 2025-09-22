<?php
declare(strict_types=1);

namespace App\Http;

final readonly class Response
{
    public function __construct(
        private int $statusCode,
        public string $body
    ) {
        http_response_code($this->statusCode);
    }
}
