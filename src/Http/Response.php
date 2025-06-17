<?php

namespace App\Http;

class Response
{
    private int $httpCode;
    private array $response;

    private array $headers = [];

    public function __construct(array $response, int $httpCode = 200)
    {
        $this->httpCode = $httpCode;
        $this->response = $response;
    }

    public function getHttpCode(): int
    {
        return $this->httpCode;
    }

    public function setHttpCode(int $httpCode): void
    {
        $this->httpCode = $httpCode;
    }

    public function getResponse(): array
    {
        return $this->response;
    }

    public function setResponse(array $response): void
    {
        $this->response = $response;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }
}