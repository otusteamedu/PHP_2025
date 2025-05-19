<?php declare(strict_types=1);

namespace App\Http;

use App\Exception\HttpException;

class Request
{
    public function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    public function getUri(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        return parse_url($uri, PHP_URL_PATH) ?: '/';
    }

    public function isUrl(string $path): bool
    {
        return rtrim($this->getUri(), '/') === rtrim($path, '/');
    }

    public function getQueryParams(): array
    {
        return $_GET;
    }

    public function getQueryParam(string $key, mixed $default = null): mixed
    {
        return $_GET[$key] ?? $default;
    }

    public function getPostData(): array
    {
        return $_POST ?? [];
    }

    public function getPostValue($key): array|string|null
    {
        return $this->getPostData()[$key] ?? null;
    }

    public function getJsonData(): array
    {
        if ($this->getContentType() === 'application/json') {
            $inputData = file_get_contents('php://input');
            $data = json_decode($inputData, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new HttpException('Invalid JSON data received');
            }

            return $data;
        }

        return [];
    }

    public function getContentType(): string
    {
        return $_SERVER['CONTENT_TYPE'] ?? '';
    }
}
