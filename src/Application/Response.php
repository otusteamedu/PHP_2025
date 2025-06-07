<?php

declare(strict_types=1);

namespace App\Application;

class Response
{
    public function __construct(
        private string $content = '',
        private int $httpCode = 200,
        private array $headers = [],
    ) {
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getHttpCode(): int
    {
        return $this->httpCode;
    }

    public function setHttpCode(int $httpCode): self
    {
        $this->httpCode = $httpCode;

        return $this;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }

    public function __toString(): string
    {
        return $this->content;
    }
}
