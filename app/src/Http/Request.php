<?php

namespace App\Http;

class Request
{
    private array $queryParams;
    private array $postParams;
    private array $serverParams;

    public function __construct()
    {
        $this->queryParams = $_GET;
        $this->postParams = $_POST;
        $this->serverParams = $_SERVER;
    }

    public function getQueryParam(string $key, $default = null)
    {
        return $this->queryParams[$key] ?? $default;
    }

    public function getPostParam(string $key, $default = null)
    {
        return $this->postParams[$key] ?? $default;
    }

    public function getServerParam(string $key, $default = null)
    {
        return $this->serverParams[$key] ?? $default;
    }

    public function getMethod(): string
    {
        return $this->serverParams['REQUEST_METHOD'] ?? 'GET';
    }

    public function getUri(): string
    {
        return $this->serverParams['REQUEST_URI'] ?? '/';
    }
}