<?php
declare(strict_types=1);

namespace App\Infrastructure\Http;

class Request
{
    private array $get;
    private array $post;
    private array $server;

    public function __construct(array $get, array $post, array $server)
    {
        $this->get = $get;
        $this->post = $post;
        $this->server = $server;
    }

    public static function fromGlobals(): self
    {
        return new self($_GET, $_POST, $_SERVER);
    }

    public function getMethod(): string
    {
        return strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
    }

    public function getPostParam(string $key, string $default = ''): string
    {
        return (string)($this->post[$key] ?? $default);
    }

    public function getQueryParam(string $key, string $default = ''): string
    {
        return (string)($this->get[$key] ?? $default);
    }

    public function getPath(): string
    {
        $uri = $this->server['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH);
        return $path ?: '/';
    }

    public function isPost(): bool
    {
        return $this->getMethod() === 'POST';
    }

    public function isGet(): bool
    {
        return $this->getMethod() === 'GET';
    }
}
