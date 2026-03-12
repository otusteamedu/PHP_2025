<?php

declare(strict_types=1);

namespace Queues\Infrastructure\Http;

use Queues\Application\Interfaces\RequestInterface;

class Request implements RequestInterface
{
    private array $get;
    private array $post;
    private array $files;

    public function __construct(
        ?array $get = null,
        ?array $post = null,
        ?array $files = null
    ) {
        $this->get = $get ?? $_GET;
        $this->post = $post ?? $_POST;
        $this->files = $files ?? $_FILES;
    }

    public function get(string $key, ?string $default = null): ?string
    {
        return $this->get[$key] ?? $default;
    }

    public function post(string $key, ?string $default = null): ?string
    {
        return $this->post[$key] ?? $default;
    }

    public function json(): array
    {
        $input = file_get_contents('php://input');
        if (!$input) {
            return [];
        }

        return json_decode($input, true, 512, JSON_THROW_ON_ERROR) ?? [];
    }

    public function files(): array
    {
        return $this->files;
    }

    public function method(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    public function path(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';
        return rtrim($path, '/') ?: '/';
    }
}
