<?php

namespace App\Http;

class Request
{
    private string $method;
    private array $queryParams;
    private array $postData;

    private string $uri;
    private array $headers;

    public function __construct()
    {
        $this->postData = $_POST;
        $this->queryParams = $_GET;
        $this->uri = $this->parseRequestUri();
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function setUri(string $uri): void
    {
        $this->uri = $uri;
    }

    private function parseRequestUri(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        return explode('?', $uri)[0];
    }

    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    public function getPostData(): array
    {
        return $this->postData;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getHeader(string $name): ?string
    {
        return $this->headers[$name] ?? null;
    }

    public function getBody(): string
    {
        return file_get_contents('php://input');
    }

    public function getArrayBody(): ?array
    {
        $body = $this->getBody();
        return json_decode($body, true) ?: null;
    }

    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }

    public function getPath(): string
    {
        return parse_url($this->uri, PHP_URL_PATH);
    }
}