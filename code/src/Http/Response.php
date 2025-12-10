<?php
declare(strict_types=1);

namespace src\Http;

class Response
{
    public function __construct(
        private string $content,
        private int $statusCode = 200
    ) {}

    public function send(): void
    {
        http_response_code($this->statusCode);
        header('Content-Type: application/json');
        echo $this->content;
    }
}
