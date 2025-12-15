<?php
declare(strict_types=1);

namespace src\Http;

class Response
{
    public function __construct(
        private string $content,
        private int $statusCode = 200
    ) {}

    public function getContent(): string
    {
        return $this->content;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
