<?php

namespace App\Http;

class Response
{
    public function __construct(private readonly int $statusCode = 200, private readonly string $body = '')
    {
    }

    public function send(): void
    {
        http_response_code($this->statusCode);
        header("Content-Type: " . 'text/html');
        echo $this->body;
    }
}