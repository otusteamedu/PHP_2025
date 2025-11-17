<?php
declare(strict_types=1);

namespace App\Infrastructure\Http;

class Request
{

    private string $method;
    private string $path;
    private array $arHeaders = [];
    private Stream $body;

    private function __construct(string $method, string $path, array $arHeaders, Stream $body)
    {
        $this->method = strtoupper($method);

        $this->path = $path;

        foreach ($arHeaders as $name => $value) {
            $this->arHeaders[strtolower($name)] = (array)$value;
        }

        $this->body = $body;
    }

    public static function fromGlobals(): self
    {
        return new self(self::getRequestMethod(), self::getRequestPath(), self::getHeadersFromWebServer(), self::getRequestBody());
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getHeaders(): array
    {
        return $this->arHeaders;
    }

    public function getHeader(string $name): array
    {
        return $this->arHeaders[strtolower($name)] ?? [];
    }

    public function hasHeader(string $name): bool
    {
        return isset($this->arHeaders[strtolower($name)]);
    }

    public function getBody(): Stream
    {
        return $this->body;
    }

    private static function getRequestMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    private static function getRequestPath(): string
    {
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
        return (string) parse_url($requestUri, PHP_URL_PATH);
    }

    private static function getHeadersFromWebServer(): array
    {
        $arHeaders = [];
        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $arHeaders[strtolower(str_replace('_', '-', substr($key, 5)))] = [$value];
            }
        }
        if (isset($_SERVER['CONTENT_TYPE'])) {
            $arHeaders['content-type'] = [$_SERVER['CONTENT_TYPE']];
        }
        if (isset($_SERVER['CONTENT_LENGTH'])) {
            $arHeaders['content-length'] = [$_SERVER['CONTENT_LENGTH']];
        }

        return $arHeaders;
    }

    private static function getRequestBody(): Stream
    {
        $raw = file_get_contents('php://input') ?: '';
        return new Stream($raw);
    }
}