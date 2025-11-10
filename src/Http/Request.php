<?php
declare(strict_types=1);

namespace App\Http;

class Request
{
    private string $method;
    private string $path;
    /** @var array<string, string[]> */
    private array $headers;
    private Stream $body;

    private function __construct(string $method, string $path, array $headers, Stream $body)
    {
        $this->method = strtoupper($method);
        $this->path = $path;
        // normalize header names to lowercase
        $this->headers = [];
        foreach ($headers as $name => $value) {
            $this->headers[strtolower($name)] = (array)$value;
        }
        $this->body = $body;
    }

    public static function fromGlobals(): self
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = (string) parse_url($requestUri, PHP_URL_PATH);

        // Build headers from $_SERVER
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $name = strtolower(str_replace('_', '-', substr($key, 5)));
                $headers[$name] = [$value];
            }
        }
        if (isset($_SERVER['CONTENT_TYPE'])) {
            $headers['content-type'] = [$_SERVER['CONTENT_TYPE']];
        }
        if (isset($_SERVER['CONTENT_LENGTH'])) {
            $headers['content-length'] = [$_SERVER['CONTENT_LENGTH']];
        }

        $raw = file_get_contents('php://input') ?: '';
        $body = new Stream($raw);

        return new self($method, $path, $headers, $body);
    }

    // Accessors (PSR-7-like)
    public function getMethod(): string
    {
        return $this->method;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    /** @return array<string, string[]> */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /** @return string[] */
    public function getHeader(string $name): array
    {
        return $this->headers[strtolower($name)] ?? [];
    }

    public function hasHeader(string $name): bool
    {
        return isset($this->headers[strtolower($name)]);
    }

    public function getBody(): Stream
    {
        return $this->body;
    }
}