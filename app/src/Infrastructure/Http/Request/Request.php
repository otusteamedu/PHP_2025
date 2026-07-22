<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Request;

final readonly class Request
{
    public function __construct(
        private string $method,
        private string $path,
        private array $body,
    ) {
    }

    public static function fromGlobals(): self
    {
        $body = json_decode(
            file_get_contents('php://input'),
            true
        );

        return new self(
            method: $_SERVER['REQUEST_METHOD'],
            path: parse_url(
                $_SERVER['REQUEST_URI'],
                PHP_URL_PATH,
            ),
            body: is_array($body) ? $body : [],
        );
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getJsonBody(): array
    {
        return $this->body;
    }
}